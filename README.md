# Chirper - Plataforma de Microblogging

Este projeto consiste em uma plataforma de microblogging desenvolvida com o framework Laravel 12. A aplicação foi estruturada para demonstrar competências em arquitetura MVC, persistência de dados e processamento dinâmico de informações através de um sistema de tópicos e tendências.

## Visão Geral
O Chirper permite a autenticação de usuários e o compartilhamento de mensagens curtas. O diferencial técnico desta implementação é o mecanismo de identificação e categorização automática de hashtags, transformando o feed em um fluxo de dados navegável e organizado por assuntos de interesse.

## Demonstração em Produção
A aplicação encontra-se publicada e disponível para testes no link abaixo:
* **Link do Projeto:** [https://chirper-joaoxavier-main-ssgdka.free.laravel.cloud/](https://chirper-joaoxavier-main-ssgdka.free.laravel.cloud/)

## Funcionalidades Técnicas

### Camada de Autorização e Segurança
A aplicação implementa políticas de acesso rigorosas através de **Laravel Policies** e **Form Requests**:
* **Controle de Acesso Granular:** Através da `ChirpPolicy`, o sistema garante que as operações de edição (`update`) e exclusão (`delete`) sejam restritas exclusivamente ao proprietário do registro.
* **Validação de Conteúdo:** Utilização de `StoreChirpRequest` para centralizar regras de validação, limitando o tamanho das postagens a 255 caracteres e fornecendo feedback personalizado ao usuário.
* **Segurança de Dados:** Prevenção contra ataques XSS (Cross-Site Scripting) na renderização de tags dinâmicas através do uso de `HtmlString` e escaping de caracteres no processamento do Model.

### Processamento de Hashtags e Tendências
* **Extração via Regex:** Utilização de expressões regulares no Model `Chirp` para identificar padrões de hashtags (#) em tempo real, permitindo a vinculação dinâmica de tópicos.
* **Análise de Coleções:** Implementação de lógica no `ChirpController` utilizando métodos de Collections do Laravel (`flatMap`, `countBy`, `sortDesc`) para gerar estatísticas de tendências (Trending Topics) dinamicamente com base no feed atual.
* **Normalização de Dados:** Padronização de strings para garantir que hashtags com diferentes variações de caixa sejam contabilizadas como um único tópico centralizado.

## Arquitetura de Software

### Modelagem de Dados (Entidades)
* **User:** Gerencia autenticação, perfis e relação de posse dos conteúdos.
* **Chirp:** Armazena as mensagens, timestamps e lógica de extração de metadados (hashtags).

### Endpoints Principais (API/Web)
* `GET /` - Visualização do feed global ou filtrado por tópico.
* `POST /chirps` - Persistência de novas mensagens (Requer Autenticação).
* `GET /chirps/{chirp}/edit` - Interface de edição de registros.
* `PUT /chirps/{chirp}` - Atualização de dados existentes (Protegido por Policy).
* `DELETE /chirps/{chirp}` - Remoção lógica de registros (Protegido por Policy).

## Tecnologias Utilizadas
* **Backend:** PHP 8.2 e Laravel 12.
* **Frontend:** Blade Templates, Tailwind CSS e DaisyUI.
* **Ferramenta de Build:** Vite.
* **Banco de Dados:** SQLite.
* **Ambiente de Hospedagem:** Laravel Cloud.

## Instruções de Configuração Local

Caso deseje executar o projeto em ambiente local, o repositório conta com um script de automação para configurar todas as dependências, chaves e banco de dados:

1. **Configuração Automática:**
   ```bash
   composer setup
   ```

2. **Executar o Ambiente:**
   ```bash
   composer dev
   ```

*Nota: O comando `setup` realiza a instalação do Composer/NPM, gera a APP_KEY, cria o arquivo de banco de dados SQLite e executa as migrações automaticamente.*
