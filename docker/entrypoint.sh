#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z mysql 3306; do   
  sleep 1 # wait for 1 second before checking again
done
echo "MySQL is up!"

# Run migrations and seed the database
php artisan migrate --force
php artisan db:seed --force

# Fetch article data from third-party APIs
php artisan fetch:article-data

# Run the application
php artisan serve --host=0.0.0.0 --port=8000
