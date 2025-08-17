#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	# The first time volumes are mounted, the project directory is empty
    if [ -d "var/cache" ] ; then
        chown -R phpuser:phpuser var
    fi
fi

exec docker-php-entrypoint "$@"
