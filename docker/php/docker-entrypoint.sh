#!/bin/sh
set -eu

prepare_dir() {
    dir="$1"

    mkdir -p "$dir"
    chmod 0777 "$dir"
}

prepare_dir /var/www/html/runtime
prepare_dir /var/www/html/runtime/logs
prepare_dir /var/www/html/runtime/debug
prepare_dir /var/www/html/runtime/cache
prepare_dir /var/www/html/runtime/mpdf
prepare_dir /var/www/html/web/imagens
prepare_dir /var/www/html/web/imagens/doacoes
prepare_dir /var/www/html/web/pdf
prepare_dir /var/www/html/web/pdf/certificados

exec docker-php-entrypoint "$@"
