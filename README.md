# Barbearia

Este repositório contém a configuração necessária para o funcionamento de um sistema de agendamentos para uma barbearia, utilizando Docker. 

## Estrutura do Projeto

No caminho `./sistema/infra`, você encontrará os seguintes arquivos de configuração:

### `mysql.yml`

Este arquivo configura um serviço de banco de dados MySQL. Ele expõe a porta definida no arquivo `.env` e permite que o usuário personalize as seguintes configurações:

- **Database**: Nome do banco de dados a ser criado.
- **User**: Nome de usuário para acesso ao banco de dados.
- **Password**: Senha do usuário do banco de dados.

### `barbearia.yml`

Este arquivo configura um servidor Apache com PHP 8. A configuração inclui:

- Extensões PHP necessárias para o funcionamento do sistema.
- Instalação do cron para agendamento de tarefas.

As definições da imagem, nome do contêiner e porta do servidor podem ser configuradas diretamente pelo arquivo `.env`.

## Configuração do Ambiente

Antes de iniciar os serviços, você deve criar um arquivo `.env` na raiz do projeto, com as seguintes variáveis:

```env
# Credenciais para o banco de dados mysql no container
MYSQL_ROOT_PASSWORD=seu_password_root
MYSQL_DATABASE=seu_database
MYSQL_USER=seu_usuario
MYSQL_PASSWORD=seu_password


# Credencias banco de dados usando docker-compose
IMAGE_NAME=use_o_dockerfile_no_mesmo_diretorio_para_contruir_a_image_e_depois_coloque_aui
CONTAINER_NAME=nome_do_seu_container
PORTs=8080:80  # ou qualquer outra porta desejada, interna e externa

# Credencias da Barbearia na Evolution API
NAME_INSTANCE=nome_da_sua_instancia
API_URL=url_da_sua_api
API_KEY=sua_api_key

```

## Como iniciar

```
docker-compose -f ./sistema/infra/mysql.yml up -d
docker-compose -f ./sistema/infra/barbearia.yml up -d
```

## Licença 

```

### Considerações:

1. **Estrutura Clara**: Organizei as seções para uma leitura mais fluida e coerente.
2. **Explicações Detalhadas**: Adicionei descrições mais completas sobre cada arquivo de configuração e suas funcionalidades.
3. **Instruções de Configuração**: Forneci um exemplo de como criar o arquivo `.env` e como iniciar os serviços, tornando mais fácil para novos usuários entenderem o processo.
4. **Contribuições e Licença**: Adicionei seções comuns em repositórios do GitHub, que ajudam a comunicar a abertura para contribuições e a questão legal do projeto.

Sinta-se à vontade para ajustar ou adicionar informações conforme necessário!
