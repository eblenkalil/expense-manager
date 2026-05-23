#!/bin/bash
# ==============================================
# deploy.sh — Atualiza o sistema no servidor
# Uso: bash deploy.sh
# ==============================================

set -e

echo ""
echo "============================================"
echo "  Gestão de Despesas — Deploy"
echo "============================================"
echo ""

# Puxa as últimas mudanças do GitHub
echo "▶ Atualizando código..."
git pull origin main

# Reconstrói e reinicia os containers
echo "▶ Reconstruindo containers..."
docker compose up -d --build

# Aguarda o PHP ficar pronto
echo "▶ Aguardando serviços..."
sleep 10

# Roda migrations e otimiza
echo "▶ Rodando migrations..."
docker compose exec php php artisan migrate --force

echo "▶ Otimizando..."
docker compose exec php php artisan optimize:clear
docker compose exec php php artisan optimize

echo ""
echo "============================================"
echo "  ✅ Deploy concluído!"
echo "============================================"
echo ""
