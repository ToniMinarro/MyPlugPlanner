UID=$(shell id -u)
GID=$(shell id -g)
DOCKER_PHP_SERVICE=php-fpm
PHP_ONLY_CHANGED_FILES := $(shell git diff --diff-filter=d --name-only HEAD | grep '.php')

SCRIPT_PATH = scripts
SCRIPT_NAME = ChargePointMonitor.sh
LOG_FILE = /tmp/charge_point_monitor.log

start: erase cache-folders build composer-install up

erase:
	docker compose down -v

build:
	docker compose build && \
	docker compose pull

cache-folders:
	mkdir -p ~/.composer && chown ${UID}:${GID} ~/.composer

composer-install:
	docker compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} composer install

up:
	docker compose up -d

stop:
	docker compose stop

down: ## alias stop
	make stop

bash:
	docker compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} bash

root:
	docker compose run --rm -u 0:0 ${DOCKER_PHP_SERVICE} sh

logs:
	docker compose logs -f ${DOCKER_PHP_SERVICE}

fix-perms:
	docker compose run --rm -u ${0}:${0} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off chown -Rvf ${UID}:${GID} /var/www/html/*"

fix_style:
	docker compose exec --user=${UID}:${GID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off vendor/bin/ecs check --fix $(PHP_ONLY_CHANGED_FILES)"

fix_rector:
	docker compose exec --user=${UID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off vendor/bin/rector process $(PHP_ONLY_CHANGED_FILES)"

fix: fix_rector fix_style

grumphp:
	docker compose exec --user ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off grumphp run"

cache-clear:
	docker compose run --rm -u ${0}:${0} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off php bin/console cache:clear"

consume-commands:
	docker compose exec --user ${UID}:${GID} ${DOCKER_PHP_SERVICE} console messenger:consume commands_low --bus=execute_command.bus --limit=100 --time-limit=60 -vv

consume-events:
	docker compose exec --user ${UID}:${GID} ${DOCKER_PHP_SERVICE} console messenger:consume events --bus=execute_event.bus --limit=100 --time-limit=60 -vv

consume-wms-events:
	docker compose exec --user ${UID}:${GID} ${DOCKER_PHP_SERVICE} console messenger:consume wms_events --bus=execute_event.bus --limit=100 --time-limit=60 -vv

consume-oms-events:
	docker compose exec --user ${UID}:${GID} ${DOCKER_PHP_SERVICE} console messenger:consume oms_events --bus=execute_event.bus --limit=100 --time-limit=60 -vv

phinx_migrate:
	docker compose exec --user=${UID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off phinx migrate"

phinx_create:
	@read -p "Enter migration name: " name; \
	docker compose exec --user=${UID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off vendor/bin/phinx create $$name"

phinx_rollback:
	docker compose exec --user=${UID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off phinx rollback"

phinx_fixtures:
	docker compose exec --user=${UID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off phinx seed:run"

test_unit:
	docker compose exec --user ${UID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off vendor/bin/phpunit --no-coverage"

test_unit_coverage:
	docker compose exec --user ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=coverage vendor/bin/phpunit"

behat:
	docker-compose exec --user=${UID} ${DOCKER_PHP_SERVICE} sh -c "XDEBUG_MODE=off ./vendor/bin/behat --colors"

fixAssets:
	@echo "Creando symlinks individuales en public/ para las carpetas de assets..."

	@for dir in css js plugins media styles; do \
		if [ -L public/$$dir ]; then \
			echo "Symlink ya existe: public/$$dir"; \
		elif [ -e public/$$dir ]; then \
			echo "ERROR: public/$$dir ya existe como carpeta o archivo. Elimínalo manualmente para crear el symlink."; \
		else \
			ln -s ../assets/$$dir public/$$dir && echo "Symlink creado: public/$$dir -> ../assets/$$dir"; \
		fi \
	done

# Inicia el script en segundo plano
monitor-start:
	@echo "Iniciando $(SCRIPT_NAME)..."
	@nohup ./$(SCRIPT_PATH)/$(SCRIPT_NAME) > $(LOG_FILE) 2>&1 & echo $$! > /tmp/$(SCRIPT_NAME).pid
	@echo "Ejecutando en segundo plano (PID: $$(cat /tmp/$(SCRIPT_NAME).pid))"

# Detiene todas las instancias del script
monitor-stop:
	@echo "Deteniendo $(SCRIPT_NAME)..."
	@./$(SCRIPT_PATH)/$(SCRIPT_NAME) --stop

# Muestra los logs en tiempo real
monitor-logs:
	@echo "Mostrando logs de $(SCRIPT_NAME)..."
	@tail -f $(LOG_FILE)