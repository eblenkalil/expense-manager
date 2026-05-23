#!/bin/bash
# =============================================================
# scripts/monitor.sh
# Monitora o log do Laravel e formata erros para o Claude Code
# Uso: bash scripts/monitor.sh [--watch]
# =============================================================

LOG_FILE="storage/logs/laravel.log"
LAST_CHECK_FILE=".claude/last_monitor_check"
OUTPUT_FILE=".claude/errors_report.md"

# ─── Cores ───────────────────────────────────────────────────
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
NC='\033[0m'

check_errors() {
  if [ ! -f "$LOG_FILE" ]; then
    echo -e "${YELLOW}Log não encontrado: $LOG_FILE${NC}"
    exit 1
  fi

  # Pega timestamp da última verificação
  LAST_CHECK="1970-01-01 00:00:00"
  if [ -f "$LAST_CHECK_FILE" ]; then
    LAST_CHECK=$(cat "$LAST_CHECK_FILE")
  fi

  # Atualiza timestamp
  mkdir -p .claude
  date '+%Y-%m-%d %H:%M:%S' > "$LAST_CHECK_FILE"

  echo -e "${CYAN}========================================${NC}"
  echo -e "${CYAN}  Monitor de Erros — Gestão de Despesas${NC}"
  echo -e "${CYAN}  Verificando desde: $LAST_CHECK${NC}"
  echo -e "${CYAN}========================================${NC}"
  echo ""

  # Extrai blocos de erro do log
  ERRORS=$(grep -n "ERROR\|CRITICAL\|EMERGENCY" "$LOG_FILE" | tail -50)

  if [ -z "$ERRORS" ]; then
    echo -e "${GREEN}✅ Nenhum erro encontrado no log.${NC}"
    echo ""
    exit 0
  fi

  # Conta erros únicos
  UNIQUE_ERRORS=$(echo "$ERRORS" | grep -oP '\[\d{4}-\d{2}-\d{2}.*?\].*?:.*' | sort | uniq -c | sort -rn)

  echo -e "${RED}⚠️  Erros encontrados:${NC}"
  echo ""

  # Gera relatório em markdown para o Claude Code
  cat > "$OUTPUT_FILE" << REPORT
# Relatório de Erros — $(date '+%d/%m/%Y %H:%M')

Use este relatório com o comando \`/fix-logs\` no Claude Code.

## Erros encontrados

\`\`\`
$(tail -n 500 "$LOG_FILE" | grep -A 5 "ERROR\|CRITICAL\|EMERGENCY" | head -200)
\`\`\`

## Resumo

$(echo "$ERRORS" | wc -l) ocorrência(s) de erro(s) encontrada(s).

## Instrução para o agente

Analise os erros acima, identifique a causa raiz de cada um,
corrija no código Laravel e faça o commit da correção.
REPORT

  echo "$ERRORS" | while read -r line; do
    echo -e "  ${RED}→${NC} $line"
  done

  echo ""
  echo -e "${CYAN}Relatório salvo em: $OUTPUT_FILE${NC}"
  echo -e "${CYAN}Execute no Claude Code: /fix-logs${NC}"
  echo ""
}

# ─── Modo watch (monitora continuamente) ─────────────────────
if [ "$1" == "--watch" ]; then
  echo -e "${CYAN}Monitorando logs em tempo real (Ctrl+C para parar)...${NC}"
  echo ""

  PREV_SIZE=0
  while true; do
    CURR_SIZE=$(wc -l < "$LOG_FILE" 2>/dev/null || echo 0)
    if [ "$CURR_SIZE" -gt "$PREV_SIZE" ]; then
      NEW_ERRORS=$(tail -n $((CURR_SIZE - PREV_SIZE)) "$LOG_FILE" | grep "ERROR\|CRITICAL")
      if [ -n "$NEW_ERRORS" ]; then
        echo -e "${RED}[$(date '+%H:%M:%S')] Novo erro detectado!${NC}"
        echo "$NEW_ERRORS"
        echo ""
      fi
      PREV_SIZE=$CURR_SIZE
    fi
    sleep 10
  done
else
  check_errors
fi
