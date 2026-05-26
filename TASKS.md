# TASKS

Lista de tarefas pendentes para o Claude Code executar neste repositório.
Executar uma por vez, commitando cada uma separadamente com mensagem descritiva.
Antes de qualquer alteração visual, ler o UI_STYLE_GUIDE.md.

---

## 1. Corrigir preview de recibo (404)

O modal de visualização de recibo de despesa está retornando 404.
Investigar como o arquivo é armazenado (storage path) e como a URL é gerada.
Garantir que o link aponte para a rota correta e que o arquivo seja servido com autenticação quando necessário.

---

## 2. Corrigir modal de recibo (UX)

O modal de preview do recibo está ocupando a tela inteira.
Transformar em um slide-over lateral ou modal centralizado com tamanho adequado.
Seguir o UI_STYLE_GUIDE.md para estilo do modal (rounded-xl, border, shadow-sm, overlay slate-900/40).

---

## 3. Seleção de despesas para gerar relatório

Na listagem de despesas ("Minhas Despesas"), adicionar:
- Checkbox em cada linha de despesa com status "available"
- Barra de ação flutuante que aparece ao selecionar ao menos uma despesa
- Botão "Gerar Relatório" na barra de ação
- Modal ou slide-over para preencher título do relatório, chave PIX do usuário e confirmar as despesas selecionadas
- Ao confirmar, criar o relatório e associar as despesas selecionadas, mudando o status delas para "locked"

---

## 4. Botão de confirmar relatório fica desabilitado indevidamente

Na criação do relatório, o botão de confirmação permanece desabilitado mesmo após as despesas serem selecionadas.
O usuário precisa desselecionar e selecionar novamente para o botão ser habilitado.
Investigar o estado reativo do componente Livewire e corrigir a lógica de habilitação do botão para que reflita corretamente a seleção atual.

---

## 5. Corrigir dashboard

- Fundo da página deve ser bg-slate-50 (atualmente aparece branco)
- Seção do gráfico "Despesas reembolsadas por mês" deve ter o card container (rounded-xl border border-slate-200 bg-white p-6)

---

## 6. Datas do gráfico em português

No dashboard, o gráfico de barras "Despesas reembolsadas por mês" exibe os meses em inglês.
Garantir que os labels do eixo X estejam em português (ex: "jan/25", "fev/25").
Verificar o locale do Carbon/PHP usado para gerar os labels no componente Livewire do dashboard.
Usar translatedFormat ou setLocale('pt_BR') para formatar corretamente.

---

## 7. Aplicar UI_STYLE_GUIDE.md em todas as telas

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

---

## 8. PDF do relatório — fonte uniforme

No relatório de despesas gerado em PDF, a fonte não está consistente entre as seções.
Garantir que todo o documento use uma única família de fonte (preferencialmente DejaVu Sans ou similar compatível com dompdf).
Definir a fonte globalmente no CSS do template PDF em vez de por elemento.

---

## 9. PDF do relatório — alinhamento do status

No relatório PDF, a coluna de status das despesas está desalinhada.
Corrigir o alinhamento para que fique centralizado ou alinhado à direita de forma consistente com as demais colunas.

---

## 10. PDF do relatório — incluir chave PIX

Adicionar campo "Chave PIX" no formulário de criação do relatório.
Salvar a chave PIX no modelo Report (adicionar migration se necessário).
Exibir a chave PIX no template PDF do relatório, em seção de "Dados para Pagamento", logo abaixo do total a reembolsar.
A chave PIX é a informação de pagamento principal e deve estar visível no PDF.

---

## 11. Download dos anexos junto com o PDF

Na tela de visualização do relatório, adicionar botão "Baixar Anexos".
Ao clicar, compactar todos os arquivos anexados nas despesas vinculadas ao relatório em um arquivo ZIP.
Disponibilizar o ZIP para download imediato.
O nome do arquivo deve seguir o padrão: `anexos-{protocol_number}.zip`.

---

## 12. Despesas vinculadas a relatório somem da listagem

Atualmente, despesas com status "locked" (vinculadas a um relatório) somem completamente da listagem "Minhas Despesas".
O comportamento correto é:
- Despesas com status "available": aparecem normalmente na listagem
- Despesas com status "locked": aparecem na listagem com badge indicando o relatório ao qual estão vinculadas, mas sem possibilidade de edição ou nova seleção
- Despesas com status "archived": não aparecem na listagem principal (já reembolsadas)
Ajustar o filtro da query e o badge de status na listagem.

---

## 13. Cadastro de usuários e perfis

Implementar gerenciamento de usuários e permissões:
- Tela de listagem de usuários (apenas para administradores)
- Formulário de criação e edição de usuário (nome, email, senha, perfil)
- Perfis disponíveis:
  - admin: acesso total ao sistema, vê dados de todos os usuários
  - collaborator: cadastra despesas e gera relatórios próprios, vê apenas seus próprios dados
- Controle de acesso por perfil em todas as rotas e componentes Livewire
- Proteção de rotas administrativas via middleware ou policy

---

## 14. Painel admin/financeiro — gestão de relatórios pendentes

No perfil admin, criar tela de gestão de relatórios submetidos para pagamento:
- Listagem de todos os relatórios com status "submitted" de todos os colaboradores
- Exibir: colaborador, protocolo, valor total, data de submissão
- Botão de ação "Pagar":
  - Abre modal para anexar comprovante de pagamento (upload de arquivo)
  - Ao confirmar, muda status do relatório para "paid" e registra o comprovante
- Botão de ação "Reprovar":
  - Abre modal para informar o motivo da reprovação (campo de texto obrigatório)
  - Ao confirmar, muda status do relatório para "rejected" e notifica o colaborador
- O comprovante de pagamento deve ser visualizável pelo colaborador na tela do relatório

---

## Instruções gerais para o Claude Code

- Ler o UI_STYLE_GUIDE.md antes de qualquer alteração visual
- Nunca criar colunas ad hoc para campos dinâmicos
- Sempre limpar cache após alterações em views: php artisan view:clear
- Sempre rodar migrations após alterações no banco: php artisan migrate
- Commitar cada tarefa separadamente com mensagem descritiva em português
- Testar o fluxo completo de cada funcionalidade antes de passar para a próxima
