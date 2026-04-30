#!/usr/bin/env sh
set -eu

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Waiting for database and running migrations..."
    attempts=0

    until php artisan migrate --force --no-interaction; do
        attempts=$((attempts + 1))

        if [ "$attempts" -ge 60 ]; then
            echo "Migrations failed after 60 attempts." >&2
            exit 1
        fi

        echo "Migration attempt $attempts failed. Retrying in 2 seconds..."
        sleep 2
    done
fi

exec "$@"
