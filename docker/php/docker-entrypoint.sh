#!/bin/sh
set -eu

prepare_dir() {
    dir="$1"

    mkdir -p "$dir"
    chmod 0777 "$dir"
}

migrate_donation_files() {
    legacy_dir="/var/www/html/web/imagens/doacoes"
    protected_dir="/var/www/html/storage/doacoes"

    [ -d "$legacy_dir" ] || return 0

    for legacy_file in "$legacy_dir"/*; do
        [ -f "$legacy_file" ] || continue
        mv -n "$legacy_file" "$protected_dir"/
    done
}

prepare_dir /var/www/html/runtime
prepare_dir /var/www/html/runtime/logs
prepare_dir /var/www/html/runtime/debug
prepare_dir /var/www/html/runtime/cache
prepare_dir /var/www/html/runtime/mpdf
prepare_dir /var/www/html/web/assets
prepare_dir /var/www/html/web/img
prepare_dir /var/www/html/web/imagens
prepare_dir /var/www/html/storage/doacoes
prepare_dir /var/www/html/web/pdf
prepare_dir /var/www/html/web/pdf/certificados
migrate_donation_files

exec docker-php-entrypoint "$@"
