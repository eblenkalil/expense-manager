# /review — Revisão completa de código antes do commit

Faça uma revisão detalhada de todas as mudanças não commitadas.

## O que verificar

### 1. Qualidade do código
- Há `dd()`, `dump()`, `var_dump()`, `console.log()` esquecidos?
- Há credenciais, senhas ou tokens hardcoded?
- Há queries N+1 (loops com consultas ao banco dentro)?
- Há lógica de negócio em controllers (deveria estar no Livewire)?
- Os e-mails usam `->queue()` e não `->send()`?

### 2. Segurança
- Inputs do usuário estão sendo validados com `#[Validate]`?
- Uploads verificam o tipo MIME real do arquivo?
- Rotas de admin têm o middleware `admin`?
- Autorizações verificam se o recurso pertence ao usuário?

### 3. Banco de dados
- Se houve mudança no schema, existe uma migration nova?
- As migrations são reversíveis (método `down()` correto)?
- Há índices faltando em colunas muito consultadas?

### 4. Padrões do projeto
- Commits seguem o padrão `tipo: descrição em português`?
- Componentes Livewire usam lazy loading quando necessário?
- Views não contêm lógica de negócio?

### 5. Testes
```bash
php artisan test
```
- Todos os testes passando?

## Formato do relatório

Apresente o resultado assim:

```
✅ Aprovado — pode commitar
⚠️  Atenção — problemas não bloqueantes encontrados
❌ Bloqueado — problemas críticos que precisam ser corrigidos antes
```

Liste cada problema encontrado com o arquivo e linha correspondente,
e ofereça corrigir automaticamente os que puder.
