# WORKFLOW.md — Guia do Dia a Dia

Guia completo de como trabalhar com o sistema usando Claude Code como agente.

---

## 🚀 Início rápido

```bash
# 1. Entra na pasta do projeto
cd expense-manager

# 2. Inicia o Claude Code
claude

# 3. Pronto — descreva o que quer fazer em português
```

---

## 📋 Cenários comuns

### "Quero corrigir um bug"

```
Você: "Os usuários relataram que o total do relatório não bate com a soma das despesas"
Claude Code: lê o código → localiza o problema → corrige → testa → commita
```

Ou se tiver o log de erro:
```
Você: /fix-logs
Claude Code: lê o log → analisa → corrige → commita
```

---

### "Quero adicionar uma funcionalidade nova"

```
Você: /new-feature
Claude Code: pergunta o que você quer → planeja → implementa → commita
```

Ou descreva diretamente:
```
Você: "Adiciona um campo de observação no pagamento do relatório, onde o admin pode escrever como foi pago"
Claude Code: cria a migration → atualiza o Livewire component → atualiza a view → commita
```

---

### "Quero publicar no servidor"

```
Você: /deploy
Claude Code: roda os testes → compila → faz push → GitHub Actions faz o deploy automático
```

Você acompanha o deploy em:
https://github.com/eblenkalil/expense-manager/actions

---

### "Quero revisar antes de commitar"

```
Você: /review
Claude Code: analisa todo o código alterado → lista problemas → oferece corrigir
```

---

### "Preciso sincronizar o ambiente local"

```
Você: /sync-prod
Claude Code: puxa o main → instala dependências → roda migrations → compila
```

---

## 🔁 Fluxo completo de uma feature

```
1. claude                          → abre o agente
2. /sync-prod                      → sincroniza com main
3. "Implementa [feature]"          → Claude Code implementa
4. /review                         → revisa o código
5. /deploy                         → publica (testa → push → Actions → Hostinger)
```

---

## 📊 Monitoramento de erros

### Manual (quando quiser verificar)
```bash
bash scripts/monitor.sh
```

### Automático em tempo real
```bash
bash scripts/monitor.sh --watch
```

### Dentro do Claude Code
```
Você: /fix-logs
```

---

## 🔐 Configuração inicial (apenas uma vez)

### 1. Clonar o repositório localmente
```bash
git clone https://github.com/eblenkalil/expense-manager.git
cd expense-manager
```

### 2. Configurar variáveis de ambiente
```bash
cp .env.example .env
# Edite o .env com os dados do banco e e-mail
php artisan key:generate
```

### 3. Instalar e configurar
```bash
composer install
npm install
php artisan migrate --seed
php artisan storage:link
npm run dev
```

### 4. Instalar Claude Code
```bash
npm install -g @anthropic-ai/claude-code
claude  # faz login com sua conta Claude Pro
```

### 5. Configurar GitHub Secrets para o deploy automático

No GitHub: **Settings → Secrets and variables → Actions → New repository secret**

| Secret | Valor |
|--------|-------|
| `HOSTINGER_HOST` | IP ou hostname do seu Hostinger |
| `HOSTINGER_USER` | Usuário SSH (ex: `u123456789`) |
| `HOSTINGER_PATH` | Caminho do projeto (ex: `/home/u123456789/public_html/expense-manager`) |
| `HOSTINGER_SSH_KEY` | Chave SSH privada (ver abaixo) |

### Como gerar a chave SSH para o Hostinger

```bash
# Na sua máquina local
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/hostinger_deploy

# Copia a chave pública para o Hostinger
ssh-copy-id -i ~/.ssh/hostinger_deploy.pub u123456789@seudominio.com

# O conteúdo da chave PRIVADA vai no secret HOSTINGER_SSH_KEY
cat ~/.ssh/hostinger_deploy
```

---

## 🌿 Estratégia de branches

| Branch | Quando usar |
|--------|------------|
| `main` | Produção. Deploy automático ao fazer push. |
| `develop` | Integração. Testes rodam aqui. |
| `feat/nome` | Nova funcionalidade. |
| `fix/nome` | Correção de bug. |

```bash
# Claude Code cuida das branches automaticamente com /new-feature e /deploy
# Mas se precisar criar manualmente:
git checkout -b feat/minha-feature
```

---

## ❓ Dicas de uso do Claude Code

**Seja específico:**
```
❌ "Corrige o problema das despesas"
✅ "O valor total do relatório está calculando errado quando há despesas com centavos. Corrija o componente CreateReport."
```

**Cole erros completos:**
```
✅ "Temos esse erro: [cole o stack trace completo do log]"
```

**Peça confirmação antes de mudanças grandes:**
```
✅ "Antes de implementar, me mostre um plano do que vai ser alterado"
```

**Use os slash commands para tarefas repetitivas:**
```
/deploy      → publica no servidor
/review      → revisa o código
/fix-logs    → corrige erros do log
/new-feature → implementa funcionalidade
/sync-prod   → sincroniza local com main
```
