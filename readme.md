# API RESTful para Gerenciamento de Usuários (Desafio Técnico)

Esta é uma API RESTful completa para operações de CRUD de usuários, desenvolvida em PHP com o framework CodeIgniter 3. O projeto foi construído seguindo boas práticas de mercado, com foco em segurança, escalabilidade e documentação.

## ✨ Features Implementadas

Além do CRUD básico solicitado, o projeto inclui as seguintes funcionalidades para demonstrar uma abordagem mais robusta e profissional:

* **Autenticação com JWT (JSON Web Tokens):** Os endpoints de manipulação de dados são protegidos, exigindo um `Bearer Token` para acesso.
* **Endpoint de Cadastro Público:** Permite a criação do primeiro usuário para que a API possa ser testada.
* **Hashing de Senhas:** As senhas são armazenadas de forma segura no banco de dados usando o algoritmo `BCRYPT`.
* **Validação de Entrada:** Os dados recebidos são validados para garantir a integridade das informações.
* **Tratamento de Exceções Global:** Todos os erros da aplicação (incluindo erros de banco de dados) são interceptados e retornam uma resposta padronizada em formato JSON, evitando o vazamento de informações sensíveis em produção.
* **Gerenciamento de Segredos com `.env`:** Chaves de API e credenciais não são expostas no código, sendo gerenciadas de forma segura através de variáveis de ambiente.
* **Documentação Interativa com Swagger/OpenAPI:** Uma documentação completa e interativa da API é gerada automaticamente a partir do código.

## 🚀 Começando (Passo a Passo)

Siga estas instruções para configurar e executar o projeto localmente.

### 1. Pré-requisitos

* **PHP** 7.4 ou superior
* **Servidor Web Local** (XAMPP, WAMP, MAMP, etc.) com Apache e MySQL
* **Composer** ([getcomposer.org](https://getcomposer.org/))
* **Postman** (Opcional, mas recomendado para testes)
* **Git**

### 2. Instalação

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/Wolf-gangSE/users
    cd users
    ```

2.  **Instale as dependências PHP:**
    ```bash
    composer install
    ```

3.  **Configure o Banco de Dados:**
    * Inicie o MySQL no seu servidor local.
    * Crie um novo banco de dados. Você pode chamá-lo de `desafio_api`.
    * Execute o seguinte script SQL para criar a tabela `users`:
        ```sql
        CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(150) NOT NULL,
          `email` varchar(150) NOT NULL,
          `password` varchar(255) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ```

4.  **Configure as Variáveis de Ambiente:**
    * Na raiz do projeto, crie um novo arquivo chamado `.env`.
    * Adicione sua chave secreta para o JWT neste arquivo. Crie uma chave longa e aleatória.
        ```dotenv
        JWT_SECRET_KEY="SUA_CHAVE_SECRETA_LONGA_E_ALEATORIA_AQUI"
        ```

5.  **Configure o CodeIgniter:**
    * Abra o arquivo `application/config/database.php` e insira as credenciais do seu banco de dados local.
    * Abra o arquivo `application/config/config.php` e ajuste a `base_url` para a URL do seu projeto (ex: `http://localhost/users/`).


### 3. Testando a API

1.  **Importe a Coleção:**
    * No Postman, clique em "Import" e selecione o arquivo `user_api.postman_collection.json` que está na raiz do projeto.
2.  **Importe o Ambiente:**
    * Clique em "Import" novamente e selecione o arquivo `user_api.postman_environment.json` que está na raiz do projeto.
3.  **Fluxo de Teste:**
    * Execute a requisição `POST Cadastrar usuário` para criar uma conta.
    * Execute a requisição `POST Login` com as credenciais criadas. O script da requisição salvará o token JWT automaticamente na variável de ambiente `jwt_token`.
    * Agora, execute qualquer outro endpoint. A autenticação será feita automaticamente.

## 🕹️ Endpoints da API

| Verbo  | Endpoint      | Autenticação | Descrição                                  |
| :----- | :------------ | :----------- | :----------------------------------------- |
| `POST` | `/users`        | **Pública** | Cadastra um novo usuário.             |
| `POST` | `/auth/login`   | **Pública** | Autentica um usuário e retorna um token JWT. |
| `GET`  | `/users`        | **Requerida** | Lista todos os usuários.                   |
| `GET`  | `/users/{id}`   | **Requerida** | Busca um usuário específico pelo ID.         |
| `PUT`  | `/users/{id}`   | **Requerida** | Atualiza os dados de um usuário.             |
| `DELETE`| `/users/{id}`  | **Requerida** | Deleta um usuário.                         |

---