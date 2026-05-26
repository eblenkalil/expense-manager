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
- Controle de acesso por perfil em todas as rotas e componentes Livewire
- Proteção de rotas administrativas via middleware ou policy

---

## Instruções gerais para o Claude Code

- Ler o UI_STYLE_GUIDE.md antes de qualquer alteração visual
- Nunca criar colunas ad hoc para campos dinâmicos
- Sempre limpar cache após alterações em views: php artisan view:clear
- Sempre rodar migrations após alterações no banco: php artisan migrate
- Commitar cada tarefa separadamente com mensagem descritiva em português
- Testar o fluxo completo de cada funcionalidade antes de passar para a próxima
