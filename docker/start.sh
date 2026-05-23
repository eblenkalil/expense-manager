#!/bin/sh
set -e

echo "▶ Aguardando banco de dados..."
sleep 5

echo "▶ Rodando migrations..."
php artisan migrate --force

echo "▶ Populando categorias..."
php artisan db:seed --force 2>/dev/null || true

echo "▶ Criando link do storage..."
php artisan storage:link 2>/dev/null || true

echo "▶ Otimizando..."
php artisan optimize

echo "✅ Iniciando PHP-FPM..."
exec php-fpm
