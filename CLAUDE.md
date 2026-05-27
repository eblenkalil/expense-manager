# CLAUDE.md — Intranet Corporativa

> Leia este arquivo inteiro antes de qualquer ação. Ele contém tudo que você precisa saber sobre o projeto.

---

## 📌 Visão Geral

Este projeto está evoluindo de um **sistema de gestão de despesas** para uma **Intranet corporativa completa** para uma empresa de tecnologia com 40 colaboradores.
O expense-manager original é um **módulo** dentro da Intranet.

Repositório: https://github.com/eblenkalil/expense-manager

---

## 🏗️ Stack

| Camada | Tecnologia |
|--------|-----------|
| Framework | Laravel 11 |
| Frontend reativo | Livewire 3 |
| Estilo | **Tailwind CSS via Vite** |
| Componentes | **Blade Components customizados** |
| Autenticação | Laravel Breeze + **Spatie Laravel Permission** |
| PDF | barryvdh/laravel-dompdf |
| E-mail | Laravel Mail + Queue (driver: database) |
| Banco principal | MySQL no **AWS RDS** (`intranet_db`) |
| Banco SaaS | MySQL no **mesmo RDS** (`saas_db`) — somente leitura |
| Infra | EC2 Amazon Linux + Docker Compose |
| Agente de dev | Claude Code |

---

## 🎨 Design System — Tailwind

Fonte principal: **Inter** (Google Fonts) ou **Plus Jakarta Sans**.
Paleta base: `slate` para neutros, `indigo-600` como cor primária.

### Componentes padrão

**Botões:**
```html
<!-- Primário -->
<button class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">Ação</button>

<!-- Secundário -->
<button class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-xl border border-slate-200 transition-colors">Cancelar</button>

<!-- Destrutivo -->
<button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors">Excluir</button>
```

**Cards:**
```html
<div class="bg-white rounded-2xl border border-slate-200">
    <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800">Título</h3>
    </div>
    <div class="p-6">conteúdo</div>
</div>
```

**Badges de status:**
```html
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Pendente</span>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Aprovado</span>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">Reprovado</span>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-700">Pago</span>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Rascunho</span>
```

**Inputs:**
```html
<input class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
```

**Ícones:** SVG inline do Heroicons (https://heroicons.com) — sempre `w-4 h-4` ou `w-5 h-5`.

### Regras visuais
- Bordas: sempre `rounded-xl` (12px) ou `rounded-2xl` (16px)
- Sombras: evitar — usar bordas `border-slate-200`
- Fundo da página: `bg-slate-50`
- Cards e painéis: `bg-white`
- Tipografia de destaque: `font-semibold text-slate-800`
- Tipografia secundária: `text-sm text-slate-500`

---

## 👥 Perfis de usuário

Controle via **Spatie Laravel Permission** (roles + permissions):

| Perfil | Descrição |
|--------|-----------|
| `admin` | Acesso total |
| `manager` | Gestor de área — aprovações, relatórios |
| `support` | Atendimento — consulta de clientes |
| `financial` | Financeiro — despesas, relatórios |
| `employee` | Colaborador comum |

> O campo `role` na tabela `users` (sistema antigo) será migrado para Spatie.

---

## 🔄 Módulos

### ✅ Já existe no projeto
- **Expenses** — despesas, relatórios, reembolsos (Livewire)
- **Recruitment** — vagas, candidatos, entrevistas (Livewire)
- **Multi-perfil** — admin, manager, hr, collaborator (campo `role` legado)

### 🔄 Em desenvolvimento
- **Core** — Spatie permissions, departments, audit_logs
- **Clients** — consulta de clientes (SaaS DB + Bitrix24 CRM)
- **Layout Intranet** — sidebar, dashboard, navegação por módulo

### 📋 Planejados (futuro)
- Communications, Documents, Calendar, App Flutter

---

## 🗄️ Banco de dados

### Banco principal — `intranet_db`
```
users             id, name, email, password, department_id, position, phone,
                  avatar, is_active, last_login_at, role (legado → Spatie)
departments       id, name, slug, color, is_active
audit_logs        id, user_id, action, entity_type, entity_id,
                  old_values, new_values, ip_address, created_at
categories        id, name, active
expenses          id, user_id, category_id, expense_date, value, description,
                  receipt_path, status
reports           id, user_id, protocol_number, title, total_value, status...
report_expenses   pivot report_id + expense_id
jobs              fila de e-mails
```

### Banco SaaS — `saas_db` (somente leitura)
- Conexão `saas` no `config/database.php`
- Models em `App\Models\Saas\*` com `$connection = 'saas'`
- Boot bloqueia create/update/delete automaticamente
- Todo acesso registrado via `AuditService::log()`

---

## 🔗 Integrações

- **Bitrix24 CRM** — via REST API (`BITRIX_URL`, `BITRIX_TOKEN`)
- **SaaS próprio** — read-only via conexão `saas` no RDS
- **Microsoft 365** — planejado, escopo a definir

---

## 📂 Estrutura de arquivos importantes

```
app/
  Livewire/
    Dashboard.php
    Expenses/ExpenseList.php
    Reports/ReportList.php, CreateReport.php, ReportDetail.php
    Admin/AdminIndex.php, AdminReports.php, AdminUsers.php, AdminCategories.php
    Recruitment/         ← módulo de recrutamento
    Profile/ProfileSettings.php
  Models/
    User.php             ← adicionar HasRoles (Spatie)
    Department.php       ← novo
    AuditLog.php         ← novo
    Saas/Client.php      ← novo, read-only, connection='saas'
  Services/
    AuditService.php     ← novo — log de acessos sensíveis
    ProtocolService.php  ← já existe — gera REL-YYYY-XXXX
    BitrixService.php    ← novo — integração Bitrix24 CRM
  Http/Middleware/
    AdminMiddleware.php  ← já existe (migrar para Spatie gradualmente)

resources/views/
  layouts/app.blade.php  ← layout principal com sidebar Tailwind
  components/            ← Blade components reutilizáveis
  livewire/              ← views dos componentes Livewire
  reports/pdf.blade.php  ← template PDF

routes/
  web.php
  modules/               ← novo — rotas por módulo
    expenses.php
    clients.php
    admin.php
```

---

## ⚙️ Padrões de código

- **Lógica fica nos Livewire components**, não em controllers
- **Validação:** atributo `#[Validate]` nos components Livewire
- **E-mails:** sempre `Mail::to()->queue()`, nunca `send()`
- **Uploads:** `Storage::disk('public')`
- **Permissões:** `@can` nas views, `$this->authorize()` nos controllers
- **Auditoria:** `AuditService::log()` em todo acesso a dados de clientes
- **Commits:** português, formato `tipo: descrição` (feat/fix/refactor/style/docs)
- **Formatação:** `./vendor/bin/pint` após qualquer alteração PHP
- **Assets:** `npm run build` após alterar CSS/JS
- **Migrations:** sempre criar nova migration, nunca editar existente

---

## 🐳 Infraestrutura — Docker no Amazon Linux

| Item | Detalhe |
|------|---------|
| Servidor | Amazon Linux (EC2) |
| Docker Compose | Versão antiga — usar `docker-compose` (com hífen) |
| Containers | expense-mysql, expense-nginx, expense-php, expense-queue |
| Node/npm | Disponível no container php |

### Comandos do dia a dia
```bash
# Aplicação
docker-compose exec php php artisan serve
docker-compose exec php npm run dev
docker-compose exec php npm run build

# Banco
docker-compose exec php php artisan migrate
docker-compose exec php php artisan db:seed

# Cache (sempre rodar após mudanças)
docker-compose exec php php artisan optimize:clear
docker-compose exec php php artisan view:clear
docker-compose exec php php artisan permission:cache-reset

# Logs
docker-compose logs -f php
docker-compose exec php tail -f storage/logs/laravel.log

# Testes
docker-compose exec php php artisan test

# Qualidade
docker-compose exec php ./vendor/bin/pint
```

---

## 🌐 Variáveis de ambiente

```env
APP_NAME="Intranet Corporativa"
APP_URL=https://seudominio.com

# Banco principal
DB_CONNECTION=mysql
DB_HOST=seu-rds.rds.amazonaws.com
DB_DATABASE=intranet_db
DB_USERNAME=intranet_user
DB_PASSWORD=

# Banco SaaS (somente leitura)
SAAS_DB_HOST=seu-rds.rds.amazonaws.com
SAAS_DB_DATABASE=saas_db
SAAS_DB_USERNAME=saas_readonly_user
SAAS_DB_PASSWORD=

# Bitrix24
BITRIX_URL=https://suaempresa.bitrix24.com.br/rest
BITRIX_TOKEN=

# Obrigatórias (Docker)
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

---

## ✅ Checklist antes de qualquer PR

- [ ] `php artisan test` passando
- [ ] `./vendor/bin/pint` rodado
- [ ] Nenhum `dd()`, `dump()` no código
- [ ] Migration criada se houve mudança no banco
- [ ] `npm run build` rodado se houve mudança em CSS/JS
- [ ] `php artisan view:clear` rodado
- [ ] Commit com mensagem no padrão correto
