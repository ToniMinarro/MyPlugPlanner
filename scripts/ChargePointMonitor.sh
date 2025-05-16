#!/bin/bash
# charge_point_monitor.sh

#--- Función de ayuda ---
usage() {
    echo "Uso: $0 [--stop] [--foreground] [--log <log_file>] [--interval <seconds>] [--cuprId <cuprId>]"
    exit 1
}

#--- Función para enviar mensajes al bot de Telegram ---
send_telegram_message() {
    local message="$1"
    local TOKEN="8110804537:AAH1Eyd--T2ie-rxiwF8FUqTaMy790TOLx8"
    local CHAT_ID="7208553"
    
    curl -s -X POST "https://api.telegram.org/bot$TOKEN/sendMessage" \
        -d "chat_id=$CHAT_ID" \
        -d "text=$message" \
        -d "parse_mode=Markdown" > /dev/null
}

#--- Validación de dependencias ---
for cmd in curl jq notify-send; do
    if ! command -v "$cmd" &> /dev/null; then
        echo "Error: El comando '$cmd' no está instalado. Por favor, instálalo para continuar."
        exit 1
    fi
done

#--- Variables por defecto ---
LOG_FILE="/tmp/charge_point_monitor.log"
INTERVAL=60
CUPR_ID=166587
FOREGROUND=false

#--- Procesar argumentos ---
ARGS=()
while [[ "$1" != "" ]]; do
    case $1 in
        --stop)
            ARGS+=("$1")
            ;;
        --foreground|--main)
            FOREGROUND=true
            ARGS+=("$1")
            ;;
        --log)
            ARGS+=("$1")
            shift
            [[ -z "$1" ]] && usage
            LOG_FILE="$1"
            ARGS+=("$1")
            ;;
        --interval)
            ARGS+=("$1")
            shift
            [[ -z "$1" ]] && usage
            INTERVAL="$1"
            ARGS+=("$1")
            ;;
        --cuprId)
            ARGS+=("$1")
            shift
            [[ -z "$1" ]] && usage
            CUPR_ID="$1"
            ARGS+=("$1")
            ;;
        *)
            usage
            ;;
    esac
    shift
done

#--- Si se pasó --stop, detenemos todas las instancias ---
if [[ " ${ARGS[@]} " =~ " --stop " ]]; then
    CURRENT_PID=$$
    PIDS=$(pgrep -f "$(basename "$0")" | grep -v "$CURRENT_PID")

    if [[ -n "$PIDS" ]]; then
        echo "Matando instancias de $(basename "$0") en ejecución..."
        echo "$PIDS" | xargs kill 2>/dev/null
        echo "Instancias detenidas."
    else
        echo "No se encontraron instancias en ejecución de $(basename "$0")."
    fi

    exit 0
fi

#--- Relanzar el script en modo detach (si no se indicó --foreground) ---
if ! $FOREGROUND && [ -z "$DETACHED" ]; then
    export DETACHED=1
    nohup "$0" "${ARGS[@]}" > "$LOG_FILE" 2>&1 &
    echo "Script ejecutándose en segundo plano con PID $!"
    exit 0
fi

#--- Manejo de señales para una salida ordenada ---
trap 'echo "[$(date +"%Y-%m-%d %H:%M:%S")] Señal recibida, terminando..." | tee -a "$LOG_FILE"; exit 0' SIGINT SIGTERM

#--- Configuración del endpoint ---
URL="https://www.iberdrola.es/o/webclipb/iberdrola/puntosrecargacontroller/getDatosPuntoRecarga"
DATA="{\"dto\":{\"cuprId\":[$CUPR_ID]},\"language\":\"es\"}"

echo "[$(date +"%Y-%m-%d %H:%M:%S")] Monitoreo iniciado..." | tee -a "$LOG_FILE"

while true; do
    FECHA=$(date +"%Y-%m-%d %H:%M:%S")
    
    # Ejecutar curl con las cabeceras y payload indicados
    RESPUESTA=$(curl -s "$URL" \
      -H 'accept: application/json, text/javascript, */*; q=0.01' \
      -H 'accept-language: es,es-ES;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6,ca;q=0.5' \
      -H 'content-type: application/json; charset=UTF-8' \
      -H 'cookie: COOKIE_SUPPORT=true; _evga_b415={%22uuid%22:%22d5873284ad28ed93%22}; OnetrustActiveGroups=%2CC0001%2CC0002%2CC0003%2CC0004%2C...' \
      -H 'origin: https://www.iberdrola.es' \
      -H 'priority: u=1, i' \
      -H 'referer: https://www.iberdrola.es/movilidad-electrica/puntos-de-recarga' \
      -H 'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Microsoft Edge";v="128"' \
      -H 'sec-ch-ua-mobile: ?0' \
      -H 'sec-ch-ua-platform: "Linux"' \
      -H 'sec-fetch-dest: empty' \
      -H 'sec-fetch-mode: cors' \
      -H 'sec-fetch-site: same-origin' \
      -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 Edg/128.0.0.0' \
      -H 'x-requested-with: XMLHttpRequest' \
      --data-raw "$DATA")
    
    if [ -z "$RESPUESTA" ]; then
        echo "[$FECHA] Error: No se obtuvo respuesta de $URL" | tee -a "$LOG_FILE"
        sleep "$INTERVAL"
        continue
    fi

    # Extraer el estado de cada puerto usando jq
    STATUS1=$(echo "$RESPUESTA" | jq -r '.entidad[0].logicalSocket[0].status.statusCode' 2>/dev/null)
    STATUS2=$(echo "$RESPUESTA" | jq -r '.entidad[0].logicalSocket[1].status.statusCode' 2>/dev/null)
    
    if [[ -z "$STATUS1" || -z "$STATUS2" || "$STATUS1" == "null" || "$STATUS2" == "null" ]]; then
        echo "[$FECHA] Error: Respuesta JSON inesperada o inválida." | tee -a "$LOG_FILE"
        sleep "$INTERVAL"
        continue
    fi

    # Notificar continuamente si el estado es "AVAILABLE"
    if [ "$STATUS1" == "AVAILABLE" ]; then
        MSG="[$FECHA] PUERTO 1: Cargador disponible"
        echo "$MSG" | tee -a "$LOG_FILE"
        notify-send "Cargador - Puerto 1" "$MSG" 2>/dev/null
        send_telegram_message "$MSG"
    else
        echo "[$FECHA] PUERTO 1: Estado $STATUS1" | tee -a "$LOG_FILE"
    fi
    
    if [ "$STATUS2" == "AVAILABLE" ]; then
        MSG="[$FECHA] PUERTO 2: Cargador disponible"
        echo "$MSG" | tee -a "$LOG_FILE"
        notify-send "Cargador - Puerto 2" "$MSG" 2>/dev/null
        send_telegram_message "$MSG"
    else
        echo "[$FECHA] PUERTO 2: Estado $STATUS2" | tee -a "$LOG_FILE"
    fi

    sleep "$INTERVAL"
done
