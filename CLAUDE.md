# CLAUDE.md — Intranet Corporativa

> Leia este arquivo inteiro antes de qualquer ação. Ele contém tudo que você precisa saber sobre o projeto.

---

## 📌 Visão Geral

Sistema evoluindo de um **gestor de despesas** para uma **Intranet corporativa completa** para empresa de tecnologia com 40 colaboradores.

Repositório: https://github.com/eblenkalil/expense-manager

---

## 🏗️ Stack

| Camada | Tecnologia |
|--------|-----------|
| Framework | Laravel 11 |
| Admin + CRUD | **Filament v3** |
| Frontend reativo | Livewire 3 (base do Filament) |
| Estilo | **Tailwind CSS** (via Filament + Vite) |
| Autenticação | Laravel Breeze + **Spatie Laravel Permission** |
| PDF | barryvdh/laravel-dompdf |
| E-mail | Laravel Mail + Queue (driver: database) |
| Banco principal | MySQL no **AWS RDS** (`intranet_db`) |
| Banco SaaS | MySQL no **mesmo RDS** (`saas_db`) — somente leitura |
| Infra | EC2 Amazon Linux + Docker Compose |
| Agente de dev | Claude Code |

---

## 🎯 Arquitetura Filament — Dois Painéis

```
/admin      → AdminPanelProvider
              Gestores e administradores
              ├── Dashboard com widgets
              ├── Usuários e departamentos
              ├── Permissões (Spatie)
              ├── Aprovação de despesas
              ├── Todos os relatórios
              ├── Clientes (SaaS DB — somente leitura)
              ├── Categorias de despesa
              └── Recrutamento

/app        → AppPanelProvider
              Colaboradores
              ├── Dashboard pessoal
              ├── Minhas despesas
              ├── Meus relatórios
              └── Meu perfil
```

---

## ⚙️ Padrões Filament

### Resources
- Cada entidade = um Resource em `app/Filament/`
- Admin resources em `app/Filament/Admin/Resources/`
- App resources em `app/Filament/App/Resources/`

### Permissões
- Usar `shield` (plugin Filament para Spatie) para controle por Resource
- Método `canAccess()` nos panels para separar admin de colaborador
- Nunca hardcodar `role` — sempre via Spatie

### Convenções
- Sempre usar `Tables\Columns`, `Forms\Components` nativos do Filament
- Widgets de dashboard em `app/Filament/*/Widgets/`
- Custom pages em `app/Filament/*/Pages/`
- Actions em linha na tabela quando possível

### Auditoria
- `AuditService::log()` em todo acesso a dados de clientes
- Registrar em `audit_logs` via observer ou action do Filament

---

## 👥 Perfis (Spatie)

| Perfil | Painel | Descrição |
|--------|--------|-----------|
| `admin` | /admin | Acesso total |
| `manager` | /admin | Gestor — aprovações e relatórios |
| `support` | /admin | Atendimento — consulta de clientes |
| `financial` | /admin | Financeiro — despesas e relatórios |
| `employee` | /app | Colaborador comum |

---

## 🔄 Módulos

### ✅ Já existe (migrar para Filament)
- Expenses — despesas e relatórios
- Recruitment — vagas, candidatos, entrevistas
- Multi-perfil — campo `role` legado

### 🔄 Em desenvolvimento
- Core Filament — dois painéis + Spatie
- Clients — consulta SaaS DB com auditoria

### 📋 Futuro
- CRM próprio
- Comunicados, Documentos, Calendário
- App Flutter mobile

---

## 🗄️ Banco de Dados

### `intranet_db` (principal)
```
users, departments, audit_logs
categories, expenses, reports, report_expenses
jobs (fila), sessions, cache
+ tabelas Spatie (roles, permissions, model_has_roles...)
```

### `saas_db` (somente leitura)
- Conexão `saas` no `config/database.php`
- Models em `App\Models\Saas\*`
- `$connection = 'saas'` + boot() bloqueia writes
- Todo acesso via `AuditService::log()`

---

## 🔗 Integrações
- **Bitrix24** — REST API (`BITRIX_URL`, `BITRIX_TOKEN`) — futuro
- **SaaS próprio** — read-only via conexão `saas`
- **Microsoft 365** — planejado

---

## 🐳 Docker — Comandos do dia a dia

```bash
# Filament
docker-compose exec php php artisan make:filament-resource NomeResource --generate
docker-compose exec php php artisan make:filament-page NomePage
docker-compose exec php php artisan make:filament-widget NomeWidget

# Banco
docker-compose exec php php artisan migrate
docker-compose exec php php artisan db:seed --class=RolesAndPermissionsSeeder

# Cache
docker-compose exec php php artisan optimize:clear
docker-compose exec php php artisan filament:cache-components
docker-compose exec php php artisan permission:cache-reset

# Assets
docker-compose exec php php artisan filament:upgrade
docker-compose exec php npm run build

# Testes
docker-compose exec php php artisan test

# Logs
docker-compose logs -f php
```

---

## 🌐 Variáveis de Ambiente

```env
APP_NAME="Intranet Corporativa"
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=seu-rds.rds.amazonaws.com
DB_DATABASE=intranet_db
DB_USERNAME=intranet_user
DB_PASSWORD=

SAAS_DB_HOST=seu-rds.rds.amazonaws.com
SAAS_DB_DATABASE=saas_db
SAAS_DB_USERNAME=saas_readonly_user
SAAS_DB_PASSWORD=

BITRIX_URL=https://suaempresa.bitrix24.com.br/rest
BITRIX_TOKEN=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

---

## ✅ Checklist antes de qualquer commit

- [ ] `php artisan test` passando
- [ ] `./vendor/bin/pint` rodado
- [ ] Nenhum `dd()` ou `dump()` no código
- [ ] Migration criada se houve mudança no banco
- [ ] `php artisan filament:cache-components` rodado
- [ ] Commit com mensagem em português no padrão `tipo: descrição`
