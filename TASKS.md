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

## 4. Download dos anexos junto com o PDF

Na tela de visualização do relatório, adicionar botão "Baixar Anexos".
Ao clicar, compactar todos os arquivos anexados nas despesas vinculadas ao relatório em um arquivo ZIP.
Disponibilizar o ZIP para download imediato.
O nome do arquivo deve seguir o padrão: `anexos-{protocol_number}.zip`.

---

## 5. Despesas vinculadas a relatório somem da listagem

Atualmente, despesas com status "locked" (vinculadas a um relatório) somem completamente da listagem "Minhas Despesas".
O comportamento correto é:
- Despesas com status "available": aparecem normalmente na listagem
- Despesas com status "locked": aparecem na listagem com badge indicando o relatório ao qual estão vinculadas, mas sem possibilidade de edição ou nova seleção
- Despesas com status "archived": não aparecem na listagem principal (já reembolsadas)
Ajustar o filtro da query e o badge de status na listagem.

---

## 6. Cadastro de usuários e perfis

Implementar gerenciamento de usuários e permissões:
- Tela de listagem de usuários (apenas para administradores)
- Formulário de criação e edição de usuário (nome, email, senha, perfil)
- Perfis disponíveis:
  - admin: acesso total ao sistema, vê dados de todos os usuários
  - collaborator: cadastra despesas e gera relatórios próprios, vê apenas seus próprios dados
- Controle de acesso por perfil em todas as rotas e componentes Livewire
- Proteção de rotas administrativas via middleware ou policy

---

## 7. Painel admin/financeiro — gestão de relatórios pendentes

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
