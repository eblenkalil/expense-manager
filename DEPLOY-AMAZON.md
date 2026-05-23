# Deploy no Amazon Linux — Gestão de Despesas

## Pré-requisitos (já verificados)
- ✅ Amazon Linux com Docker
- ✅ 4GB RAM
- ✅ Metabase rodando na porta 3000 (não será afetado)

---

## PASSO 1 — Instalar Docker Compose (se não tiver)

```bash
# Verifica se já tem
docker compose version

# Se não tiver, instala
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

---

## PASSO 2 — Clonar o repositório

```bash
cd ~
git clone https://github.com/eblenkalil/expense-manager.git
cd expense-manager
```

---

## PASSO 3 — Configurar o .env

```bash
cp .env.docker.example .env
nano .env
```

Edite as linhas obrigatórias:

```env
APP_URL=http://IP-DO-SEU-SERVIDOR    ← coloque o IP real
DB_PASSWORD=SuaSenhaForte123!        ← escolha uma senha
DB_ROOT_PASSWORD=OutraSenha456!      ← escolha outra senha
APP_KEY=                             ← deixe vazio, será gerado
```

Salve com **Ctrl+X → Y → Enter**

---

## PASSO 4 — Gerar a APP_KEY

```bash
docker run --rm \
  -v $(pwd):/app \
  -w /app \
  php:8.2-cli \
  php artisan key:generate --force
```

---

## PASSO 5 — Subir os containers

```bash
# Torna os scripts executáveis
chmod +x docker/start.sh deploy.sh

# Sobe tudo (primeira vez demora ~5 minutos)
docker compose up -d --build
```

Acompanhe os logs:
```bash
docker compose logs -f
```

Quando aparecer **"✅ Iniciando PHP-FPM..."** está pronto!

---

## PASSO 6 — Criar o primeiro usuário admin

```bash
docker compose exec php php artisan tinker --execute="
App\Models\User::create([
    'name'     => 'Administrador',
    'email'    => 'seu@email.com',
    'password' => bcrypt('SuaSenha@123'),
    'role'     => 'admin',
]);
echo 'Admin criado!';
"
```

---

## PASSO 7 — Acessar o sistema

Abra no navegador:
```
http://IP-DO-SEU-SERVIDOR
```

---

## Atualizações futuras (1 comando)

```bash
cd ~/expense-manager && bash deploy.sh
```

---

## Comandos úteis

```bash
# Ver status dos containers
docker compose ps

# Ver logs em tempo real
docker compose logs -f

# Ver logs só do PHP
docker compose logs -f php

# Reiniciar tudo
docker compose restart

# Parar tudo
docker compose down

# Parar e apagar o banco (CUIDADO!)
docker compose down -v
```

---

## Verificar se o Metabase continua funcionando

```bash
docker ps
# O container "metabase" deve continuar aparecendo com status "Up"
```

O sistema de despesas roda na porta **80** e o Metabase continua na porta **3000** — sem interferência.
