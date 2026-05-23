# /deploy — Publica o projeto no Hostinger

Execute este fluxo completo de deploy para produção:

## Passo a passo

1. **Verifique se há mudanças não commitadas**
   - Se houver, pergunte ao usuário se deve commitar antes de continuar

2. **Rode os testes**
   ```bash
   php artisan test
   ```
   - Se algum teste falhar, **pare aqui** e informe quais testes falharam
   - Não faça deploy com testes quebrados

3. **Formate o código**
   ```bash
   ./vendor/bin/pint
   ```

4. **Compile os assets**
   ```bash
   npm run build
   ```

5. **Commit das mudanças** (se houver arquivos alterados após pint/build)
   ```bash
   git add -A
   git commit -m "chore: build assets para deploy"
   ```

6. **Push para a branch main**
   ```bash
   git push origin main
   ```

7. **Informe o usuário**
   - Que o push foi feito com sucesso
   - Que o GitHub Actions vai detectar e fazer o deploy automático
   - Que o deploy leva aproximadamente 2-3 minutos
   - Link para acompanhar: https://github.com/eblenkalil/expense-manager/actions

## Notas importantes
- Nunca faça deploy direto sem rodar os testes primeiro
- Se o build do npm falhar, verifique se há erros de sintaxe em arquivos Blade
- Após o deploy, o GitHub Actions roda `php artisan optimize` automaticamente
