# /sync-prod — Sincroniza o ambiente local com produção

Garante que o ambiente local está idêntico ao que está em produção.

## Passo a passo

1. **Puxe as últimas mudanças**
   ```bash
   git checkout main
   git pull origin main
   ```

2. **Instale dependências PHP**
   ```bash
   composer install
   ```

3. **Instale dependências JS**
   ```bash
   npm install
   ```

4. **Rode migrations pendentes**
   ```bash
   php artisan migrate
   ```

5. **Compile os assets**
   ```bash
   npm run build
   ```

6. **Limpe os caches**
   ```bash
   php artisan optimize:clear
   ```

7. **Verifique se tudo está funcionando**
   ```bash
   php artisan test
   ```

8. **Informe o resultado**
   - Quantas migrations foram rodadas
   - Se alguma dependência nova foi instalada
   - Se todos os testes passaram
