# TASKS

Lista de tarefas pendentes para o Claude Code executar neste repositório.

## 1. Corrigir preview de recibo (404)

O modal de visualização de recibo de despesa está retornando 404.
Investigar como o arquivo é armazenado (storage path) e como a URL é gerada.
Garantir que o link aponte para a rota correta e que o arquivo seja servido com autenticação quando necessário.

## 2. Corrigir modal de recibo (UX)

O modal de preview do recibo está ocupando a tela inteira.
Transformar em um slide-over lateral ou modal centralizado com tamanho adequado.
Seguir o UI_STYLE_GUIDE.md para estilo do modal (rounded-xl, border, shadow-sm, overlay slate-900/40).

## 3. Seleção de despesas para gerar relatório

Na listagem de despesas ("Minhas Despesas"), adicionar:
- Checkbox em cada linha de despesa com status "available"
- Barra de ação flutuante que aparece ao selecionar ao menos uma despesa
- Botão "Gerar Relatório" na barra de ação
- Modal ou slide-over para preencher título do relatório e confirmar as despesas selecionadas
- Ao confirmar, criar o relatório e associar as despesas selecionadas, mudando o status delas para "locked"

## 4. Corrigir dashboard

- Fundo da página deve ser bg-slate-50 (atualmente aparece branco)
- Seção do gráfico "Despesas reembolsadas por mês" deve ter o card container (rounded-xl border border-slate-200 bg-white p-6)

## 5. Aplicar UI_STYLE_GUIDE.md em todas as telas

Revisar e atualizar todas as views Blade e componentes Livewire para seguir o guia de estilo definido em UI_STYLE_GUIDE.md:
- Paleta de cores: slate-50 background, white cards, blue-600 primário
- Tipografia: text-sm, font-medium, font-semibold conforme hierarquia
- Bordas: border-slate-200, rounded-xl em cards, rounded-lg em inputs e botões
- Botões: classes padronizadas do guia (primário, secundário, fantasma)
- Inputs: h-10, rounded-lg, border-slate-300, focus:ring-blue-500/20
- Tabelas: cabeçalho bg-slate-50, linhas com divide-slate-100
- Badges de status: cores suaves (emerald, amber, red, slate)
- Animações: transition duration-150 ease-out
- Espaçamento: p-6 em cards, gap-6 entre seções
- Sidebar e header: conforme seções 15 e 16 do guia

## 6. Cadastro de usuários e perfis

Implementar gerenciamento de usuários e permissões:
- Tela de listagem de usuários (apenas para administradores)
- Formulário de criação e edição de usuário (nome, email, senha, perfil)
- Perfis disponíveis:
  - admin: acesso total ao sistema
  - collaborator: cadastra despesas e gera relatórios próprios
- Controle de acesso por perfil em todas as rotas e componentes Livewire
- Administrador vê despesas e relatórios de todos os usuários
- Colaborador vê apenas os próprios dados
- Proteção de rotas administrativas via middleware ou policy

## Instruções para o Claude Code

- Ler o UI_STYLE_GUIDE.md antes de qualquer alteração visual
- Nunca criar colunas ad hoc para campos dinâmicos
- Sempre limpar cache após alterações em views: php artisan view:clear
- Sempre rodar migrations após alterações no banco: php artisan migrate
- Commitar cada tarefa separadamente com mensagem descritiva
- Testar o fluxo completo de cada funcionalidade antes de marcar como concluída
