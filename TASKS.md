# TASKS

Lista de tarefas pendentes para o Claude Code executar neste repositório.
Executar uma por vez, commitando cada uma separadamente com mensagem descritiva.
Antes de qualquer alteração visual, ler o UI_STYLE_GUIDE.md.

---

## 1. PDF do relatório — fonte uniforme

No relatório de despesas gerado em PDF, a fonte não está consistente entre as seções.
Garantir que todo o documento use uma única família de fonte (preferencialmente DejaVu Sans ou similar compatível com dompdf).
Definir a fonte globalmente no CSS do template PDF em vez de por elemento.

---

## 2. PDF do relatório — alinhamento do status

No relatório PDF, a coluna de status das despesas está desalinhada.
Corrigir o alinhamento para que fique centralizado ou alinhado à direita de forma consistente com as demais colunas.

---

## 3. PDF do relatório — incluir chave PIX

Adicionar campo "Chave PIX" no formulário de criação do relatório.
Salvar a chave PIX no modelo Report (adicionar migration se necessário).
Exibir a chave PIX no template PDF do relatório, em seção de "Dados para Pagamento", logo abaixo do total a reembolsar.
A chave PIX é a informação de pagamento principal e deve estar visível no PDF.

---

## 4. Botão de confirmar relatório fica desabilitado indevidamente

Localizar o componente Livewire responsável pela criação do relatório (provavelmente em app/Livewire/Reports/ ou similar).
O botão de confirmação do modal permanece desabilitado mesmo após o usuário selecionar despesas.
O usuário precisa desselecionar e selecionar novamente para o botão ser habilitado, o que indica falha na reatividade.

Passos para corrigir:
1. Identificar o array ou propriedade Livewire que armazena as despesas selecionadas (ex: $selectedExpenses)
2. Verificar se o botão usa :disabled ou wire:loading baseado nessa propriedade
3. Garantir que a propriedade seja atualizada reativamente via updated() hook ou computed property
4. Se usar @entangle ou Alpine.js local, verificar se o estado Alpine está sincronizado com o Livewire
5. Testar selecionando despesas sem nenhuma interação adicional — o botão deve habilitar imediatamente

---

## 5. Download dos anexos junto com o PDF

Na tela de visualização do relatório, adicionar botão "Baixar Anexos".
Ao clicar, compactar todos os arquivos anexados nas despesas vinculadas ao relatório em um arquivo ZIP.
Disponibilizar o ZIP para download imediato.
O nome do arquivo deve seguir o padrão: `anexos-{protocol_number}.zip`.

---

## 6. Despesas vinculadas a relatório somem da listagem

Atualmente, despesas com status "locked" (vinculadas a um relatório) somem completamente da listagem "Minhas Despesas".
O comportamento correto é:
- Despesas com status "available": aparecem normalmente com opção de editar e selecionar
- Despesas com status "locked": aparecem na listagem com badge indicando o relatório ao qual estão vinculadas, mas sem possibilidade de edição ou nova seleção
- Despesas com status "archived": não aparecem na listagem principal (já reembolsadas)
Ajustar o filtro da query e o badge de status na listagem.

---

## 7. Datas do gráfico em português

No dashboard, o gráfico de barras "Despesas reembolsadas por mês" exibe os meses em inglês.
Garantir que os labels do eixo X estejam em português (ex: "jan/25", "fev/25").
Verificar o locale do Carbon/PHP usado para gerar os labels no componente Livewire do dashboard.
Usar translatedFormat com Carbon::setLocale('pt_BR') para formatar corretamente.

---

## 8a. Painel admin — listagem de relatórios pendentes de pagamento

Criar tela acessível apenas para usuários com perfil admin, em rota como /admin/reports.
Listar todos os relatórios com status "submitted" de todos os colaboradores.
Colunas da listagem: colaborador (nome), protocolo, valor total, data de submissão, ações.
Seguir UI_STYLE_GUIDE.md para estilo da tabela e badges.
Adicionar link para esta tela no menu de navegação apenas para admins.

---

## 8b. Painel admin — ação de pagar relatório

Na listagem da tarefa 8a, adicionar botão "Pagar" em cada linha.
Ao clicar, abrir modal com:
- Resumo do relatório (colaborador, protocolo, valor)
- Campo de upload obrigatório para anexar comprovante de pagamento
- Botão de confirmar pagamento
Ao confirmar:
- Salvar o comprovante no storage (ex: storage/app/private/comprovantes/{id}.pdf)
- Atualizar status do relatório para "paid"
- Registrar o path do comprovante no modelo Report (adicionar coluna payment_receipt_path via migration)
O comprovante deve ser visualizável pelo colaborador na tela de detalhe do seu relatório.

---

## 8c. Painel admin — ação de reprovar relatório

Na listagem da tarefa 8a, adicionar botão "Reprovar" em cada linha.
Ao clicar, abrir modal com:
- Resumo do relatório (colaborador, protocolo, valor)
- Campo de texto obrigatório para informar o motivo da reprovação
- Botão de confirmar reprovação
Ao confirmar:
- Atualizar status do relatório para "rejected"
- Salvar o motivo no modelo Report (adicionar coluna rejection_reason via migration se não existir)
- Mudar status das despesas vinculadas de "locked" de volta para "available"
O motivo da reprovação deve ser exibido para o colaborador na tela de detalhe do relatório.

---

## 9. Cadastro de usuários e perfis

Implementar gerenciamento de usuários e permissões:
- Tela de listagem de usuários acessível apenas para administradores
- Formulário de criação e edição de usuário (nome, email, senha, perfil)
- Perfis disponíveis:
  - admin: acesso total ao sistema, vê dados de todos os usuários e relatórios
  - collaborator: cadastra despesas e gera relatórios próprios, vê apenas seus próprios dados
  - hr: acesso exclusivo ao módulo de recrutamento (vagas e candidatos)
  - financial: acesso ao painel de relatórios pendentes de pagamento
- Um usuário pode ter múltiplos perfis simultaneamente (ex: admin + hr)
- Controle de acesso por perfil em todas as rotas e componentes Livewire
- Proteção de rotas administrativas via middleware ou policy

---

## 10a. Módulo de recrutamento — modelo de dados e vagas

Criar as migrations e models para o módulo de recrutamento:

### Tabela: jobs (vagas)
- id, title (nome da vaga), position (cargo), description (texto livre), status (open/closed), public_token (UUID único gerado automaticamente), created_by (user_id), timestamps

### Tabela: candidates (candidatos)
- id, job_id, name, email, phone, linkedin (opcional), salary_expectation (opcional), cv_path (PDF obrigatório), notes (comentário do próprio candidato ao se inscrever), status (pending/interview/hired/discarded), source (manual/public_form), created_by (user_id, nulo se veio do formulário público), timestamps

### Tabela: candidate_events (linha do tempo)
- id, candidate_id, user_id (nulo se sistema), type (status_change/comment/rating), content (texto descritivo do evento), previous_status, new_status (quando type=status_change), rating (1-5, quando type=rating), created_at

Regras:
- public_token deve ser gerado automaticamente via UUID ao criar a vaga
- cv_path armazena em storage/app/private/curriculos/{id}.pdf
- Ao mudar status do candidato, sempre criar um candidate_event do tipo status_change com o motivo informado
- Ao adicionar comentário, criar candidate_event do tipo comment
- Ao registrar avaliação, criar candidate_event do tipo rating

---

## 10b. Módulo de recrutamento — formulário público de candidatura

Criar rota pública (sem autenticação) acessível via token da vaga:
- URL: /vagas/{public_token}
- Exibir nome e descrição da vaga
- Formulário com: nome, email, telefone, LinkedIn (opcional), pretensão salarial (opcional), upload de CV em PDF (obrigatório), comentário do candidato (opcional)
- Ao submeter, criar candidate com status "pending" e source "public_form"
- Se a vaga estiver fechada (status=closed), exibir mensagem informando que não está aceitando candidaturas
- Seguir UI_STYLE_GUIDE.md para estilo do formulário público
- Não exigir login, não expor dados de outros candidatos

---

## 10c. Módulo de recrutamento — listagem e gestão de vagas (RH)

Criar tela /hr/jobs acessível apenas para usuários com perfil hr ou admin:
- Listagem de vagas com: título, cargo, status (aberta/fechada), total de candidatos por status (aguardando, entrevista, contratado, descartado)
- Botão para criar nova vaga (modal com título, cargo, descrição)
- Botão para editar vaga existente
- Botão para fechar/reabrir vaga manualmente
- Botão para copiar o link público tokenizado da vaga
- Ao clicar na vaga, ir para a tela de candidatos daquela vaga (tarefa 10d)
- Adicionar link "Recrutamento" no menu de navegação visível apenas para perfis hr e admin

---

## 10d. Módulo de recrutamento — listagem de candidatos por vaga (RH)

Criar tela /hr/jobs/{id}/candidates acessível apenas para perfis hr e admin:
- Listagem de candidatos com: nome, email, pretensão salarial, status (badge colorido), data de inscrição, origem (manual/formulário público)
- Filtros por status e origem
- Botão para adicionar candidato manualmente (modal com os mesmos campos do formulário público)
- Ao clicar no candidato, abrir tela de detalhe (tarefa 10e)
- Contador de candidatos por status exibido no topo da página

Status e cores dos badges:
- pending (aguardando): amber
- interview (em entrevista): blue
- hired (contratado): emerald
- discarded (descartado): slate

---

## 10e. Módulo de recrutamento — detalhe do candidato e linha do tempo (RH)

Criar tela /hr/candidates/{id} acessível apenas para perfis hr e admin:

### Seção de dados do candidato:
- Nome, email, telefone, LinkedIn, pretensão salarial, origem, data de inscrição
- Botão para visualizar/baixar CV em PDF
- Badge de status atual

### Botões de ação de status (sempre visíveis, qualquer status pode mudar para qualquer outro):
- "Mover para Entrevista": abre modal com campo de motivo (opcional) e campo de avaliação 1-5 estrelas + texto de avaliação estruturada
- "Contratar": abre modal com campo de motivo obrigatório
- "Descartar": abre modal com campo de motivo obrigatório
- O status atual fica destacado e o botão correspondente fica desabilitado

### Seção de comentários (apenas perfil hr e admin):
- Campo de texto para adicionar novo comentário
- Comentários existentes podem ser editados pelo autor
- Comentários não podem ser deletados (apenas editados)

### Linha do tempo (cronológica, do mais recente ao mais antigo):
- Mudanças de status: ícone de seta, descrição "Status alterado de X para Y por [usuário] — Motivo: [motivo]"
- Avaliações: ícone de estrela, nota (1-5) e texto da avaliação, nome do avaliador
- Comentários de RH: ícone de balão, texto do comentário, nome do autor, data (editável)
- Inscrição inicial: ícone de entrada, "Candidato inscrito via [formulário público/cadastro manual]", comentário do candidato se houver

---

## 10f. Módulo de recrutamento — reconhecimento de candidato recorrente

Ao cadastrar um candidato (manual ou formulário público), verificar se já existe um candidato com o mesmo email no sistema.
Se existir:
- Exibir aviso para o RH: "Este candidato já se inscreveu anteriormente" com link para o histórico anterior
- Permitir prosseguir com a nova candidatura normalmente (cria novo registro de candidate vinculado à nova vaga)
- Na tela de detalhe do candidato, exibir seção "Candidaturas anteriores" com links para outras vagas que o candidato já se inscreveu

---

## 10g. Módulo de recrutamento — exportação de candidatos

Na tela de listagem de candidatos por vaga (tarefa 10d), adicionar botão "Exportar":
- Exportar lista filtrada (respeitando filtros de status e origem ativos) em CSV
- Colunas: nome, email, telefone, pretensão salarial, status, origem, data de inscrição
- Nome do arquivo: `candidatos-{titulo-da-vaga}-{data}.csv`

---


## 11. Logo da empresa no cabeçalho e no PDF

O logo da Veloce Tech foi adicionado ao repositório em `public/images/logo.png` (PNG com fundo transparente).

### Cabeçalho das páginas:
- Localizar o layout principal da aplicação (provavelmente resources/views/layouts/app.blade.php ou similar)
- Substituir o ícone/logo atual do Laravel pelo logo da empresa
- Usar a tag: `<img src="{{ asset('images/logo.png') }}" alt="Veloce Tech" class="h-8 w-auto">`
- Manter o logo no lado esquerdo do header, discreto, com altura h-8
- O logo deve ser clicável e redirecionar para o dashboard

### PDF do relatório de despesas:
- Localizar o template Blade usado para gerar o PDF (provavelmente resources/views/reports/pdf.blade.php ou similar)
- Adicionar o logo no canto superior esquerdo do cabeçalho do PDF
- Usar caminho absoluto para o dompdf encontrar o arquivo: `storage_path()` ou `public_path('images/logo.png')`
- Tamanho recomendado no PDF: largura máxima de 120px, altura automática
- O logo deve aparecer em todas as páginas do PDF se o relatório tiver múltiplas páginas

---

## 12. Página pública de candidatura — design corporativo

Redesenhar completamente a view da rota pública /vagas/{public_token} para ter uma aparência corporativa e profissional.

### Layout geral:
- Tela dividida em duas colunas em desktop (lg:grid-cols-2), empilhada em mobile
- Altura mínima de tela cheia: min-h-screen
- Sem header/navbar do sistema interno — página totalmente independente

### Coluna esquerda (painel da empresa):
- Fundo: gradiente de slate-900 para blue-900 (`bg-gradient-to-br from-slate-900 to-blue-900`)
- Centralizado verticalmente com flex
- Logo da empresa no topo: `<img src="{{ asset('images/logo.png') }}" class="h-12 w-auto brightness-0 invert">` (logo branco via invert)
- Abaixo do logo: nome da vaga em texto grande branco (`text-3xl font-semibold text-white`)
- Cargo em texto azul claro (`text-blue-300 text-lg`)
- Linha divisória sutil (`border-t border-white/10 my-6`)
- Descrição da vaga em texto branco/70 (`text-white/70 text-sm leading-relaxed`)
- Rodapé da coluna: texto pequeno com o nome da empresa (`text-white/40 text-xs`)
- Padding generoso: p-12 em desktop, p-8 em mobile

### Coluna direita (formulário):
- Fundo: bg-white
- Centralizado verticalmente com flex
- Padding: p-10 em desktop, p-6 em mobile
- Título do formulário: "Sua candidatura" em text-2xl font-semibold text-slate-900
- Subtítulo: "Preencha os dados abaixo para se candidatar" em text-sm text-slate-500
- Espaçamento entre título e formulário: mt-8

### Campos do formulário (seguir UI_STYLE_GUIDE.md para inputs):
Todos os inputs: `h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20`
Labels: `text-sm font-medium text-slate-700`
Campos obrigatórios marcados com asterisco vermelho

Ordem dos campos:
1. Nome completo* 
2. E-mail* 
3. Telefone* 
4. LinkedIn (opcional) — com placeholder "https://linkedin.com/in/seu-perfil"
5. Pretensão salarial (opcional) — campo numérico com prefixo "R$"
6. Currículo em PDF* — input file estilizado com área de drop visual (border-dashed border-2 border-slate-300 rounded-lg p-6 text-center)
7. Mensagem / comentário (opcional) — textarea de 3 linhas com placeholder "Conte um pouco sobre você ou por que tem interesse nesta vaga"

### Botão de envio:
- Largura total: w-full
- Estilo primário do UI_STYLE_GUIDE: bg-blue-600 hover:bg-blue-700
- Texto: "Enviar candidatura"
- Com estado de loading via wire:loading

### Estados especiais:
- Se a vaga estiver fechada (status=closed): mostrar apenas a coluna esquerda com mensagem centralizada "Esta vaga não está aceitando candidaturas no momento" em texto branco, sem formulário
- Após envio com sucesso: substituir o formulário por mensagem de confirmação com ícone de check verde, texto "Candidatura enviada com sucesso!" e subtexto "Entraremos em contato em breve."

### Responsividade mobile:
- Em mobile: coluna esquerda compacta (py-8 px-6) com logo + nome da vaga apenas (sem descrição completa)
- Coluna direita ocupa o restante da tela
- Sem scroll horizontal



## 13. Vagas — seletor de empresa e rich text na descrição

### 13a. Seletor de empresa nas vagas

As empresas são fixas e devem ser definidas em config, não em banco de dados.

Criar arquivo `config/companies.php`:
```php
return [
    'tri_rs'       => 'Tri.RS',
    'veloce_tech'  => 'Veloce.Tech',
    'tche_ofertas' => 'TcheOfertas',
];
```

Adicionar coluna `company` (string, nullable) na tabela `jobs` via migration.

No formulário de criação e edição de vaga (modal ou tela de RH):
- Adicionar campo select "Empresa" com as três opções acima
- Campo obrigatório
- Seguir estilo do UI_STYLE_GUIDE.md para selects: mesmas classes dos inputs com h-10

Na listagem de vagas (tarefa 10c):
- Exibir o nome da empresa como badge ou coluna na tabela
- Adicionar filtro por empresa na listagem

Na página pública de candidatura (tarefa 12):
- Exibir o nome da empresa na coluna esquerda, abaixo do cargo
- Usar texto em blue-200 com tamanho text-sm

### 13b. Rich text na descrição da vaga com TipTap

Usar TipTap via CDN (sem npm) integrado com Alpine.js para o campo de descrição da vaga.

Instalação via CDN no layout ou na view específica:
```html
<script src="https://cdn.jsdelivr.net/npm/@tiptap/core@2/dist/tiptap-core.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tiptap/starter-kit@2/dist/tiptap-starter-kit.umd.min.js"></script>
```

Implementar componente Alpine.js para o editor:
- Toolbar com botões: Negrito, Itálico, Lista com marcadores, Lista numerada, Título H2, Título H3, Separador horizontal
- Estilo da toolbar: fundo bg-slate-50, borda border-slate-200, botões com hover:bg-slate-100, botão ativo com bg-blue-50 text-blue-600
- Área de edição: min-h de 150px, padding p-3, fundo branco, borda border-slate-200 rounded-b-lg
- O valor HTML do editor deve ser sincronizado com a propriedade Livewire via Alpine `$wire`

Salvar o campo `description` como HTML no banco (TEXT).

Na exibição da vaga (página pública e tela de detalhe do RH):
- Renderizar o HTML salvo com `{!! $job->description !!}`
- Adicionar classes de tipografia ao container: `prose prose-sm max-w-none text-slate-700`
- Garantir que o Tailwind Typography (@tailwindcss/typography) esteja instalado ou usar estilos manuais equivalentes para h2, h3, ul, ol, strong, em

---

## 14. Correções visuais urgentes

### 14a. Logo não aparece no header

O logo está exibindo como texto "Veloce Tech" em vez da imagem.
Localizar o layout principal (resources/views/layouts/app.blade.php ou similar) e corrigir a tag img do logo.
Verificar se o arquivo existe em public/images/logo.png.
Se necessário, rodar `php artisan storage:link` e ajustar o path.
A tag correta deve ser:
`<img src="{{ asset('images/logo.png') }}" alt="Veloce Tech" class="h-8 w-auto">`

### 14b. Select "Todas as empresas" muito pequeno

Na tela de listagem de vagas (Recrutamento), o select de filtro por empresa está com largura insuficiente.
Aumentar a largura mínima do select para min-w-48 ou w-56.
Garantir que o texto "Todas as empresas" caiba em uma única linha sem truncar.

---


---

## 16. Layout da Intranet — Sidebar e Dashboard em Tailwind

Reescrever o layout principal da aplicação com identidade de Intranet corporativa.
Usar Tailwind CSS puro com Blade Components. Sem frameworks externos.

### 16a. Atualizar layout principal (app.blade.php)

Reescrever `resources/views/layouts/app.blade.php` com:
- Sidebar fixo à esquerda (`w-64`), colapsável para `w-16` com Alpine.js
- Fundo do sidebar: `bg-slate-900`
- Topbar: `bg-white`, `border-b border-slate-200`, altura `h-16`
- Área de conteúdo: `bg-slate-50`, padding `p-6`
- Fonte: Inter ou Plus Jakarta Sans via Google Fonts
- Flash messages com auto-dismiss via Alpine.js
- Responsivo: sidebar vira drawer no mobile

### 16b. Criar Blade Component: sidebar

Criar `resources/views/components/sidebar.blade.php`:
- Logo da empresa no topo
- Itens de menu com ícones SVG Heroicons
- Itens visíveis por permissão usando `@can`
- Seção "Administração" separada visível apenas para admin/manager
- Footer com nome, e-mail do usuário e botão de sair
- Estado ativo destacado: `bg-indigo-600 text-white`
- Estado hover: `hover:bg-slate-800`

### 16c. Criar Blade Component: topbar

Criar `resources/views/components/topbar.blade.php`:
- Título da página atual
- Busca rápida (campo com Alpine.js)
- Ícone de notificações com badge
- Avatar com dropdown: perfil e sair

### 16d. Criar Blade Component: stats-card

Criar `resources/views/components/stats-card.blade.php` com props:
- `label`, `value`, `icon` (SVG), `color` (indigo/emerald/amber/sky), `trend`

### 16e. Atualizar Dashboard

Atualizar `resources/views/livewire/dashboard.blade.php`:
- Usar novos Blade Components (stats-card)
- Cards de stats visíveis por `@can`
- Tabela de despesas recentes com badges de status Tailwind
- Seção de acesso rápido com botões por permissão
- Usar apenas classes Tailwind — sem classes customizadas externas

Após cada subtarefa: `docker-compose exec php php artisan view:clear`

---

## 17. Fundação da Intranet — Spatie, Departamentos e Auditoria

### 17a. Instalar Spatie Laravel Permission

```bash
docker-compose exec php composer require spatie/laravel-permission
docker-compose exec php php artisan vendor:publish --provider="Spatie\Permission\PermissionRegistrar" --tag="permission-migrations"
docker-compose exec php php artisan vendor:publish --provider="Spatie\Permission\PermissionRegistrar" --tag="permission-config"
docker-compose exec php php artisan migrate
```

Adicionar `use Spatie\Permission\Traits\HasRoles;` e `use HasRoles;` no model `User`.

### 17b. Migration: adicionar campos à tabela users

Nova migration com:
- `department_id` (foreignId nullable, constrained departments, nullOnDelete)
- `position` (string nullable) — cargo
- `phone` (string 20 nullable)
- `avatar` (string nullable)
- `is_active` (boolean default true)
- `last_login_at` (timestamp nullable)

Rodar após criar.

### 17c. Migration: departments

Campos: `id`, `name`, `slug` (unique), `description` (nullable), `color` (string 7, default '#6366f1'), `is_active` (boolean default true), `timestamps`.

### 17d. Migration: audit_logs

Campos: `id`, `user_id` (foreignId nullable nullOnDelete), `action`, `description` (nullable), `entity_type` (nullable), `entity_id` (nullable), `old_values` (json nullable), `new_values` (json nullable), `ip_address` (string 45), `user_agent` (nullable), `url` (nullable), `method` (string 10 nullable), `created_at` (timestamp useCurrent) — sem `updated_at`.
Índices em: user_id, [entity_type, entity_id], action, created_at.

### 17e. Criar Models

**app/Models/Department.php** — fillable, cast is_active como boolean, hasMany users.

**app/Models/AuditLog.php** — `$timestamps = false`, cast old/new_values como array, belongsTo user withTrashed.

**app/Models/Saas/Client.php** — `$connection = 'saas'`, `$table = 'clients'`, `$timestamps = false`, boot() com `RuntimeException` em creating/updating/deleting.

### 17f. RolesAndPermissionsSeeder

Criar `database/seeders/RolesAndPermissionsSeeder.php`.

Permissões: `clients.view`, `clients.export`, `expenses.view`, `expenses.create`, `expenses.edit`, `expenses.delete`, `expenses.approve`, `expenses.admin`, `users.view`, `users.create`, `users.edit`, `users.delete`, `reports.view`, `reports.export`, `announcements.view`, `announcements.create`.

Perfis:
- `admin` → todas
- `manager` → clients.view, expenses.view, expenses.approve, reports.view, reports.export, announcements.view
- `support` → clients.view, announcements.view
- `financial` → expenses.view, expenses.approve, expenses.admin, reports.view, reports.export, announcements.view
- `employee` → expenses.view, expenses.create, expenses.edit, announcements.view

Criar usuário admin: `admin@intranet.com` / `password`, assignRole('admin').
Rodar: `docker-compose exec php php artisan db:seed --class=RolesAndPermissionsSeeder`

### 17g. AuditService

Criar `app/Services/AuditService.php` com método estático `log(action, description, entityType, entityId, oldValues, newValues)`.
Captura automaticamente: user_id, ip, user_agent, url, method do request atual.

### 17h. Estrutura de rotas modular

Criar `routes/modules/` com `admin.php`, `clients.php`, `expenses.php`.
Atualizar `routes/web.php` com require dos três arquivos.

### 17i. Verificação final

```bash
docker-compose exec php php artisan optimize:clear
docker-compose exec php php artisan permission:cache-reset
docker-compose exec php php artisan test
```

---

## 18. Conexão Read-Only ao Banco SaaS

### 18a. Adicionar conexão 'saas' em config/database.php

```php
'saas' => [
    'driver'    => 'mysql',
    'host'      => env('SAAS_DB_HOST', '127.0.0.1'),
    'port'      => env('SAAS_DB_PORT', '3306'),
    'database'  => env('SAAS_DB_DATABASE'),
    'username'  => env('SAAS_DB_USERNAME'),
    'password'  => env('SAAS_DB_PASSWORD'),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => true,
],
```

### 18b. Adicionar ao .env.example (sem valores)

```
SAAS_DB_HOST=
SAAS_DB_PORT=3306
SAAS_DB_DATABASE=
SAAS_DB_USERNAME=
SAAS_DB_PASSWORD=
BITRIX_URL=
BITRIX_TOKEN=
```

### 18c. Testar conexão

```bash
docker-compose exec php php artisan tinker --execute="try { DB::connection('saas')->getPdo(); echo 'OK'; } catch(\Exception \$e) { echo \$e->getMessage(); }"
```

---

## 19. Módulo de Clientes — Consulta com Auditoria

Implementar tela de consulta de clientes da base SaaS com log de auditoria obrigatório.

### 19a. ClientController

Criar `app/Http/Controllers/Clients/ClientController.php`:
- `index()` — listagem com busca por nome/e-mail/CNPJ
- `show($id)` — detalhes do cliente
- Ambos com `$this->authorize('clients.view')` e `AuditService::log()`

### 19b. Views de clientes

Criar `resources/views/clients/index.blade.php` e `show.blade.php`:
- Usar layout e design system Tailwind definidos na task 16
- Tabela com paginação, campo de busca
- Badges de status do cliente
- Sem botões de edição/exclusão (somente leitura)

### 19c. Verificação

Logar como usuário com `clients.view`, acessar `/clients` e confirmar que:
- Dados aparecem corretamente
- Audit log registra o acesso
- Usuário sem permissão recebe 403

---

## Instruções gerais para o Claude Code

- Sempre usar `docker-compose exec php` para rodar comandos no container
- Nunca editar migrations já existentes — sempre criar nova migration
- Sempre limpar cache após alterações em views: `php artisan view:clear`
- Sempre rodar migrations após alterações no banco: `php artisan migrate`
- Usar apenas Tailwind CSS — sem frameworks externos de UI
- Ícones: SVG inline do Heroicons (heroicons.com) — tamanhos `w-4 h-4` ou `w-5 h-5`
- Sempre verificar permissões com @can nas views e authorize() nos controllers
- Registrar AuditService::log() em todo acesso a dados de clientes
- Commitar cada tarefa separadamente com mensagem descritiva em português
- Testar o fluxo completo antes de passar para a próxima tarefa
- Rodar `php artisan test` antes de qualquer commit
