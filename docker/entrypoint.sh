#!/bin/sh
set -e

# Start nginx in the background
nginx

# Execute the main container command (php-fpm)
exec "$@"
