# /new-feature — Implementa uma nova funcionalidade completa

Guia o processo de implementação de ponta a ponta.

## Passo a passo

### 1. Entendimento
Pergunte ao usuário (se não estiver claro):
- O que exatamente a funcionalidade deve fazer?
- Quem vai usar? (colaborador, admin ou ambos?)
- Há alguma regra de negócio específica?
- Existe alguma tela de referência ou exemplo?

### 2. Planejamento
Antes de escrever qualquer código, apresente um plano:
- Quais arquivos serão criados ou modificados
- Se precisa de migration nova
- Se precisa de novo componente Livewire ou apenas modificar um existente
- Estimativa de complexidade

**Aguarde aprovação do usuário antes de continuar.**

### 3. Branch
```bash
git checkout develop 2>/dev/null || git checkout main
git pull
git checkout -b feat/nome-da-feature
```

### 4. Implementação
- Migration (se necessário): `php artisan make:migration`
- Model (se necessário): `php artisan make:model`
- Livewire component (se necessário): `php artisan make:livewire`
- Implemente seguindo os padrões do CLAUDE.md
- Adicione rota em `routes/web.php` se necessário
- Adicione item no sidebar em `layouts/app.blade.php` se necessário

### 5. Qualidade
```bash
./vendor/bin/pint
php artisan test
npm run build
```

### 6. Commit
```bash
git add -A
git commit -m "feat: [descrição da funcionalidade]"
git push origin feat/nome-da-feature
```

### 7. Resumo final
Informe ao usuário:
- O que foi implementado
- Como usar a nova funcionalidade
- Se há alguma configuração manual necessária
- Link para abrir o PR: https://github.com/eblenkalil/expense-manager/compare
