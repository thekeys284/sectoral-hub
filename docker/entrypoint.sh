#!/bin/sh

# Exit immediately if a command exits with a non-zero status.
set -e

# Wait for the database to be ready
# This is a simple loop, for robust production you might use a tool like dockerize
echo "Waiting for database to be ready..."
while ! nc -z db 3306; do
  sleep 1
done
echo "Database is ready."

# Run database migrations
php artisan migrate --force

# Optimize configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Execute the container's main process (what's set as CMD in the Dockerfile)
exec "$@"