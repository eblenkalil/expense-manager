# CLAUDE.md — Gestão de Despesas

> Leia este arquivo inteiro antes de qualquer ação. Ele contém tudo que você precisa saber sobre o projeto.

---

## 📌 Visão Geral

Sistema web de gestão de despesas corporativas construído em Laravel 11 + Livewire 3.
Repositório: https://github.com/eblenkalil/expense-manager

**Propósito:** Colaboradores cadastram despesas com recibos, agrupam em relatórios de entrega e submetem para reembolso. O administrativo confirma o pagamento.

---

## 🏗️ Stack

| Camada | Tecnologia |
|--------|-----------|
| Framework | Laravel 11 |
| Frontend reativo | Livewire 3 |
| Estilo | Tailwind CSS (via Vite) |
| Autenticação | Laravel Breeze (email + senha) |
| PDF | barryvdh/laravel-dompdf |
| E-mail | Laravel Mail + Queue (driver: database) |
| Banco | MySQL 8 |
| Hospedagem | Hostinger (PHP 8.2, SSH ativo) |
| Agente de dev | Claude Code |

---

## 👥 Perfis de usuário

| Perfil | Permissões |
|--------|-----------|
| `collaborator` | Cadastra despesas, cria e entrega relatórios, vê os seus |
| `admin` | Vê todos os relatórios, confirma pagamentos, gerencia usuários e categorias |

Controle feito pela coluna `role` na tabela `users`. Middleware: `AdminMiddleware`.

---

## 🔄 Fluxo de status

```
DESPESA:
  available → locked (vinculada a relatório) → archived (relatório pago)

RELATÓRIO:
  draft → submitted (pendente pagamento) → paid (concluído)
```

Quando um relatório vai para `paid`, todas as suas despesas vão para `archived` e somem da lista principal.

---

## 🗄️ Banco de dados

```
users             id, name, email, password, role, notify_email
categories        id, name, active
expenses          id, user_id, category_id, expense_date, value, description,
                  receipt_path, receipt_original_name, status
reports           id, user_id, protocol_number, title, total_value, notes,
                  status, payment_receipt_path, payment_receipt_name,
                  submitted_at, paid_at
report_expenses   id, report_id, expense_id  (pivot)
jobs              tabela de fila para e-mails (queue:table)
```

**Número de protocolo:** formato `REL-2025-0001`, gerado por `App\Services\ProtocolService`.

---

## 📂 Estrutura de arquivos importantes

```
app/
  Livewire/
    Dashboard.php               ← stats + gráfico Chart.js
    Expenses/ExpenseList.php    ← lista + modal + upload + preview
    Reports/
      ReportList.php            ← lista com filtros
      CreateReport.php          ← seleção de despesas + criação
      ReportDetail.php          ← detalhes + entrega + pagamento
    Admin/
      AdminIndex.php            ← container de abas
      AdminReports.php          ← todos os relatórios
      AdminUsers.php            ← gerenciar perfis
      AdminCategories.php       ← CRUD de categorias
    Profile/ProfileSettings.php ← dados + senha + notificações
  Mail/
    ReportSubmittedMail.php     ← e-mail para admin quando entregue
    ReportPaidMail.php          ← e-mail para colaborador quando pago
  Services/
    ProtocolService.php         ← gera REL-YYYY-XXXX
  Http/
    Controllers/
      ReportPdfController.php   ← download do PDF
    Middleware/
      AdminMiddleware.php       ← protege rotas admin

resources/views/
  layouts/app.blade.php         ← layout com sidebar
  livewire/                     ← views de cada componente
  reports/pdf.blade.php         ← template do PDF
  mail/                         ← templates de e-mail

routes/web.php                  ← todas as rotas
bootstrap/app.php               ← registra middleware 'admin'
```

---

## 🛣️ Rotas principais

```
GET  /dashboard          → Livewire\Dashboard
GET  /expenses           → Livewire\Expenses\ExpenseList
GET  /reports            → Livewire\Reports\ReportList
GET  /reports/create     → Livewire\Reports\CreateReport
GET  /reports/{report}   → Livewire\Reports\ReportDetail
GET  /reports/{report}/pdf → ReportPdfController@download
GET  /admin              → Livewire\Admin\AdminIndex  [middleware: admin]
GET  /profile            → Livewire\Profile\ProfileSettings
```

---

## ⚙️ Padrões de código obrigatórios

- **Lógica fica nos Livewire components**, não em controllers
- **Validação:** sempre usar atributo `#[Validate]` nos components
- **E-mails:** sempre via Queue (`Mail::to()->queue()`), nunca `send()`
- **Uploads:** usar `Storage::disk('public')`, nunca mover manualmente
- **Roles:** verificar com `auth()->user()->isAdmin()`, nunca comparar string diretamente
- **Formatação:** Laravel Pint (`./vendor/bin/pint`) após qualquer alteração PHP
- **Assets:** rodar `npm run build` após alterar CSS/JS
- **Migrations:** sempre criar nova migration, nunca editar migration existente
- **Commits:** mensagens em português, formato `tipo: descrição`
  - `feat:` nova funcionalidade
  - `fix:` correção de bug
  - `refactor:` refatoração sem mudança de comportamento
  - `style:` apenas formatação
  - `docs:` documentação

---

## 🧪 Testes

```bash
php artisan test                    # roda todos os testes
php artisan test --filter NomeTest  # roda teste específico
```

Antes de commitar, sempre rodar `php artisan test` para garantir que nada quebrou.

---

## 🚀 Comandos do dia a dia

```bash
# Desenvolvimento local
php artisan serve                   # inicia servidor local
npm run dev                         # inicia Vite (watch)
php artisan queue:work              # processa fila de e-mails

# Banco
php artisan migrate                 # roda migrations pendentes
php artisan migrate:rollback        # desfaz última migration
php artisan db:seed                 # popula categorias + admin

# Cache (sempre rodar após mudanças em produção)
php artisan optimize:clear          # limpa todos os caches
php artisan optimize                # recria caches

# Qualidade
./vendor/bin/pint                   # formata código PHP
npm run build                       # compila assets

# Storage
php artisan storage:link            # cria link simbólico (apenas uma vez)

# Debug
php artisan pail                    # logs em tempo real
tail -f storage/logs/laravel.log    # logs do servidor
```

---

## 🌐 Variáveis de ambiente necessárias

```env
APP_URL=https://seudominio.com
DB_DATABASE=expense_manager
DB_USERNAME=...
DB_PASSWORD=...
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@seudominio.com
MAIL_PASSWORD=...
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

---

## 🔐 GitHub Secrets necessários (para CI/CD)

```
HOSTINGER_HOST       → IP ou hostname SSH do Hostinger
HOSTINGER_USER       → usuário SSH (ex: u123456789)
HOSTINGER_SSH_KEY    → chave SSH privada (gerada com ssh-keygen)
HOSTINGER_PATH       → caminho absoluto do projeto (ex: /home/u123456789/public_html/expense-manager)
```

---

## 🏠 Estrutura de branches

```
main        → produção (protegida, deploy automático via GitHub Actions)
develop     → integração (testes rodam aqui)
feat/*      → novas funcionalidades
fix/*       → correções de bugs
```

Nunca commitar direto na `main`. Sempre abrir PR de `feat/*` ou `fix/*` → `develop` → `main`.

---

## ✅ Checklist antes de qualquer PR

- [ ] `php artisan test` passando
- [ ] `./vendor/bin/pint` rodado
- [ ] Nenhum `dd()`, `dump()` ou `var_dump()` no código
- [ ] Migration criada se houve mudança no banco
- [ ] `npm run build` rodado se houve mudança em CSS/JS
- [ ] Commit com mensagem no padrão correto
