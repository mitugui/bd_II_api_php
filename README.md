
# API RESTful em PHP - Gerenciamento de Usuários

## Descrição do Projeto
Este projeto foi desenvolvido como parte do Trabalho Final da disciplina **Banco de Dados II** do curso de **Tecnólogo em Análise e Desenvolvimento de Sistemas (TADS)**, turma **TADS24**, no **Instituto Federal do Paraná (IFPR)**.

Trata-se de uma **API RESTful em PHP puro (sem framework)**, com suporte a autenticação via **JWT**, organização modular com **autoload PSR-4**, e ambiente de execução orquestrado com **Docker + Docker Compose**. O projeto simula operações CRUD de usuários autenticados e demonstra conceitos de segurança e boas práticas em APIs.

---

## Autores do projeto
- `Beatriz Yoshimi Yukizaki`
- `Cristian Oliveira Mitugui`
- `Gabrielly Lopes Pontes`

---

## Professor
- `Wagner Rodrigo Weinert`

---

## Cliente Front End
- Aplicação em [https://github.com/yoshimidevz/client-api-react](https://github.com/yoshimidevz/client-api-react)

---

## Tecnologias Utilizadas

- **PHP 8.2**
- **MySQL 8**
- **JWT (JSON Web Token)** para autenticação
- **Docker e Docker Compose**
- **Composer com PSR-4 Autoload**

---

## Funcionalidades Implementadas

- Registro de usuários (`POST /users`)
- Login com geração de JWT (`POST /login`)
- Acesso autenticado com verificação de token (`Bearer Token`)
- Recuperação de usuário logado com `GET`, `PUT`, `PATCH`, `DELETE /users`
- Organização modular: `Controllers`, `Config`, `Db/Migrations`, `Jwt`, `Middleware`
- Controle de rotas baseado em URI + método HTTP (sem uso de frameworks)

---

## Organização e Fluxo da Aplicação

A estrutura da aplicação está organizada da seguinte forma:

```
BD_II_API_PHP/
│
├── initdb/                      # Scripts SQL executados na inicialização do container MySQL
│   └── init.sql
│
├── public/                      # Pasta pública do Apache (DocumentRoot)
│   ├── .htaccess                # Redireciona requisições para index.php
│   └── index.php                # Roteador principal (interpreta URI e método HTTP)
│
├── src/                         # Código-fonte organizado por responsabilidade
│   ├── Config/                  # Configuração de banco de dados
│   ├── Controllers/             # Controladores (lógica das rotas)
│   ├── Db/Migrations/           # Scripts SQL de criação e alteração de tabelas
│   ├── Jwt/                     # Lógica de geração e validação de tokens
│   └── Middleware/              # Middleware para validação de autenticação
│
├── vendor/                     # Dependências gerenciadas pelo Composer
├── .env                        # Variáveis de ambiente (chaves, senhas)
├── .env.example
├── docker-compose.yml          # Orquestração dos containers (Apache, MySQL, phpMyAdmin)
├── dockerfile                  # Imagem customizada do PHP com Apache
├── composer.json               # Configuração do Composer (com PSR-4)
└── composer.lock
```

### 🔄 Fluxo Geral de Execução

1. O arquivo `index.php` dentro da pasta `public/` atua como **roteador**. Ele interpreta o método da requisição (GET, POST, PUT, PATCH, DELETE) e a URI acessada.
2. A lógica das rotas está **manual e centralizada** em `index.php`, que utiliza o autoload do Composer para chamar os métodos apropriados nos controladores.
3. O autoload PSR-4 foi configurado no `composer.json`:

```json
"autoload": {
  "psr-4": {
    "CoMit\\ApiBd\\": "src/"
  }
}
```

4. As rotas protegidas requerem um token JWT enviado via **Authorization: Bearer <token>**.
5. O middleware (`AuthMiddleware`) garante que apenas o próprio usuário possa alterar, excluir ou visualizar seus dados, validando o token e extraindo o ID do payload.

---

## Exemplo de Uso da API

### 🔑 Login

`POST /login`

```json
{
  "nome": "usuario_exemplo",
  "senha": "senha123"
}
```

**Resposta:**

```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6..."
}
```

---

### 👤 Operações com Usuário

Todas as rotas abaixo requerem **token JWT no cabeçalho Authorization**:

```
Authorization: Bearer <seu_token_aqui>
```

#### Cadastrar novo usuário

`POST /users`

```json
{
  "nome": "testeman",
  "email": "testeman@email.com",
  "senha": "senha123"
}
```

#### Buscar todos os usuários

`GET /users`

#### Buscar usuário por ID

`GET /users?id=3`

#### Atualizar totalmente (PUT)

`PUT /users`

#### Atualizar parcialmente (PATCH)

`PATCH /users`

#### Deletar usuário

`DELETE /users`

---

## Instruções para Execução com Docker

### Pré-requisitos

- Docker
- Docker Compose

### Passos

1. Clone o repositório:

```bash
git clone https://github.com/mitugui/bd_II_api_php.git
```

2. Copie o arquivo `.env.example` para `.env` e configure:

```env
DB_ROOT_PASSWORD=senha_root
DB_NAME=nome_do_banco
DB_USER=usuario
DB_PASSWORD=senha
SECRET_KEY=sua-chave-secreta
```

3. Suba os containers:

```bash
docker-compose up -d
```

4. Acesse a aplicação em [http://localhost:8080](http://localhost:8080)

5. Acesse o phpMyAdmin em [http://localhost:8081](http://localhost:8081) com:
   - **Usuário:** root
   - **Senha:** definida no `.env`

---

## Considerações Finais

Este projeto foi desenvolvido com fins **didáticos**, como parte da avaliação final da disciplina **Banco de Dados II**, e demonstra o uso prático de conceitos como:

- Organização de código por responsabilidades
- Boas práticas com PSR-4
- Segurança com autenticação JWT
- Contêineres Docker para ambiente padronizado

---
---
###### Importante: Este projeto foi desenvolvido exclusivamente para fins acadêmicos. Algumas decisões de implementação foram tomadas com foco na aprendizagem, e não refletem necessariamente padrões ideais para produção.
