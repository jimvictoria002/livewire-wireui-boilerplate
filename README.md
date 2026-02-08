# 🚀 Getting Started

Ready to fire up the engine? Follow these steps to get your local development environment running smoothly.

### 📋 Prerequisites
Ensure your machine meets the following requirements before proceeding:
- **WSL**
- **DOCKER**

### 🛠️ Installation Guide

Run the following commands in your terminal to set up the project from scratch:

```bash

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
    
cp .env.example .env

./vendor/bin/sail up -d

./vendor/bin/sail artisan key:generate

./vendor/bin/sail artisan migrate --seed

./vendor/bin/sail npm install

./vendor/bin/sail npm run build

./vendor/bin/sail artisan test --parallel

```
The admin credential is at the .env
