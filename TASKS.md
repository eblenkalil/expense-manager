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
## Instruções gerais para o Claude Code

- Ler o UI_STYLE_GUIDE.md antes de qualquer alteração visual
- Nunca criar colunas ad hoc para campos dinâmicos
- Sempre limpar cache após alterações em views: php artisan view:clear
- Sempre rodar migrations após alterações no banco: php artisan migrate
- Commitar cada tarefa separadamente com mensagem descritiva em português
- Testar o fluxo completo de cada funcionalidade antes de passar para a próxima
