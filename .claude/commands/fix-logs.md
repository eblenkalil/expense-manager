# /fix-logs — Analisa erros do log e corrige automaticamente

Leia, analise e corrija os erros mais recentes do log do Laravel.

## Passo a passo

1. **Leia os últimos 100 erros do log**
   ```bash
   tail -n 200 storage/logs/laravel.log | grep -A 10 "ERROR\|CRITICAL\|Exception"
   ```

2. **Agrupe os erros por tipo**
   - Identifique erros únicos (ignore repetições do mesmo erro)
   - Ordene por frequência (mais frequente primeiro)

3. **Para cada erro único, informe:**
   - Tipo do erro (ex: `QueryException`, `ViewException`, etc.)
   - Arquivo e linha onde ocorreu
   - Contexto (qual rota/componente estava sendo usado)
   - Quantas vezes ocorreu nas últimas 24h

4. **Proponha e aplique a correção**
   - Leia o arquivo onde o erro ocorreu
   - Entenda a causa raiz
   - Implemente a correção
   - Explique o que foi corrigido e por quê

5. **Verifique se a correção não quebrou nada**
   ```bash
   php artisan test
   ```

6. **Commit da correção**
   ```bash
   git add -A
   git commit -m "fix: [descrição do que foi corrigido]"
   ```

## Erros para ignorar
- `NOTICE` e `INFO` (não são erros)
- Erros de CSRF (geralmente sessão expirada, comportamento normal)
- Erros 404 de arquivos estáticos como `favicon.ico`

## Se o erro for desconhecido
Se não conseguir identificar a causa, apresente o stack trace completo
e explique o que cada parte significa para o usuário poder decidir.
