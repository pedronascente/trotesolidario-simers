# README Docker

Este arquivo descreve como configurar o banco, importar um dump e subir a aplicacao com Docker neste projeto.

## 1. Estrutura Docker do projeto

O projeto possui os seguintes servicos em [`docker-compose.yml`](/home/pedro/project/trotesolidario/docker-compose.yml):

- `nginx`: exposto em `http://localhost:8080`
- `php`: container PHP-FPM 8.2
- `mysql-db`: MySQL 5.7 exposto na porta `3309` da maquina host

## 2. Configuracoes de banco

As variaveis padrao estao em [`.env`](/home/pedro/project/trotesolidario/.env):

```env
DB_CONNECTION=mysql
DB_HOST=mysql-db
DB_PORT=3306
DB_DATABASE=trotesolidario
DB_USERNAME=trote
DB_PASSWORD=trote
```

A aplicacao Yii2 usa estas credenciais em [`src/config/db.php`](/home/pedro/project/trotesolidario/src/config/db.php):

```php
$dbHost = getenv('DB_HOST') ?: 'mysql-db';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_DATABASE') ?: 'trotesolidario';
$dbUser = getenv('DB_USERNAME') ?: 'trote';
$dbPassword = getenv('DB_PASSWORD') ?: 'trote';
```

## 3. Ponto de atencao importante

Hoje o container MySQL usa no `docker-compose.yml`:

- `MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}`
- `MYSQL_DATABASE: ${DB_DATABASE}`
- `MYSQL_USER: ${DB_USERNAME}`
- `MYSQL_PASSWORD: ${DB_PASSWORD}`

O servico `php` agora tambem recebe o arquivo `.env`, e [`src/config/db.php`](/home/pedro/project/trotesolidario/src/config/db.php) passou a ler:

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Ou seja, a aplicacao deixa de depender de credenciais fixas no codigo e passa a usar o `.env` como fonte principal.

Agora o `docker-compose.yml` e a aplicacao usam o mesmo conjunto de variaveis do `.env`.

Se quiser mudar as credenciais no futuro, o ajuste principal fica centralizado em [`.env`](/home/pedro/project/trotesolidario/.env).

## 4. Como subir a aplicacao com Docker

Na raiz do projeto, execute:

```bash
docker compose up -d --build
```

Se sua maquina usa o binario legado:

```bash
docker-compose up -d --build
```

A aplicacao ficara disponivel em:

```text
http://localhost:8080
```

## 5. Instalar dependencias do PHP

Se for a primeira subida ou se `vendor/` ainda nao estiver preparado, rode:

```bash
docker compose run --rm php composer install
```

## 6. Subir apenas o banco antes da importacao

Se quiser preparar o banco primeiro:

```bash
docker compose up -d mysql-db
```

## 7. Como importar um dump `.sql`

Com os containers ativos, importe o dump manualmente para o banco `trotesolidario`.

Exemplo com um arquivo `dump.sql` na raiz do projeto:

```bash
docker compose exec -T mysql-db mysql -uroot -ptrote trotesolidario < dump.sql
```

Se o arquivo estiver em outro caminho:

```bash
docker compose exec -T mysql-db mysql -uroot -ptrote trotesolidario < /caminho/para/seu-arquivo.sql
```

Observacoes:

- nesse projeto, a senha de `root` e a senha do usuario da aplicacao seguem `DB_PASSWORD`, que no `.env` atual esta como `trote`
- o banco importado no exemplo e `trotesolidario`
- o `-T` evita problemas de TTY ao redirecionar o arquivo

## 8. Importar usando o usuario da aplicacao

Tambem e possivel importar com o usuario `trote`:

```bash
docker compose exec -T mysql-db mysql -utrote -ptrote trotesolidario < dump.sql
```

## 9. Como acessar o MySQL via Docker

Para abrir um shell dentro do container do MySQL:

```bash
docker compose exec mysql-db bash
```

Para conectar no MySQL como `root`:

```bash
mysql -uroot -ptrote
```

Se preferir conectar direto sem entrar antes no shell do container:

```bash
docker compose exec mysql-db mysql -uroot -ptrote
```

Depois de conectar no cliente MySQL, voce pode usar:

Mostrar os databases:

```sql
SHOW DATABASES;
```

Selecionar o database do projeto:

```sql
USE trotesolidario;
```

Mostrar as tabelas do database selecionado:

```sql
SHOW TABLES;
```

Se quiser fazer tudo em um comando so, sem entrar no cliente interativamente:

```bash
docker compose exec mysql-db mysql -uroot -ptrote -e "SHOW DATABASES;"
docker compose exec mysql-db mysql -uroot -ptrote -e "USE trotesolidario; SHOW TABLES;"
```

## 10. Como conectar no MySQL pelo MySQL Workbench

Com o container `mysql-db` em execucao, voce pode conectar pelo MySQL Workbench usando a porta publicada na maquina host.

Primeiro, confirme que o banco esta ativo:

```bash
docker compose up -d mysql-db
```

No MySQL Workbench, crie uma nova conexao com estes dados:

- Connection Name: `trotesolidario-docker`
- Connection Method: `Standard (TCP/IP)`
- Hostname: `127.0.0.1`
- Port: `3309`
- Username: `root`
- Password: `trote`

Se preferir acessar com o usuario da aplicacao, use:

- Username: `trote`
- Password: `trote`

Depois de salvar a conexao:

1. Clique em `Test Connection`.
2. Se a conexao funcionar, clique em `OK`.
3. Abra a conexao criada.
4. No painel `Schemas`, localize o database `trotesolidario`.

Se o schema nao aparecer de imediato, clique no botao de refresh do painel `Schemas` ou execute:

```sql
SHOW DATABASES;
USE trotesolidario;
SHOW TABLES;
```

Observacoes:

- o host `mysql-db` funciona apenas entre containers; no Workbench da sua maquina, use `127.0.0.1`
- a porta correta para o Workbench e `3309`, porque ela esta mapeada do host para o container
- se a conexao falhar, confirme se o container `mysql-db` esta em execucao com `docker compose ps`

## 11. Alternativa sem dump: migrations e seeds

Se voce nao tiver um dump e quiser montar a base pela aplicacao:

Aplicar migrations:

```bash
docker compose exec php php yii migrate --interactive=0
```

Carregar seeds:

```bash
docker compose exec php php yii seed/all
```

O comando de seeds existe em [`src/commands/SeedController.php`](/home/pedro/project/trotesolidario/src/commands/SeedController.php).

## 12. Acessos e portas

- Aplicacao web: `http://localhost:8080`
- MySQL pela maquina host: `127.0.0.1:3309`
- MySQL entre containers: `mysql-db:3306`

## 13. Comandos uteis

Subir tudo:

```bash
docker compose up -d
```

Ver logs:

```bash
docker compose logs -f
```

Parar containers:

```bash
docker compose down
```

Remover containers e volume do banco:

```bash
docker compose down -v
```

Use `down -v` com cuidado, porque isso apaga os dados persistidos do MySQL em `mysql-data`.

## 14. Fluxo recomendado para ambiente novo

1. Conferir [`.env`](/home/pedro/project/trotesolidario/.env) e [`src/config/db.php`](/home/pedro/project/trotesolidario/src/config/db.php).
2. Subir os containers com `docker compose up -d --build`.
3. Rodar `docker compose run --rm php composer install`.
4. Importar o dump com `docker compose exec -T mysql-db mysql -uroot -ptrote trotesolidario < dump.sql`.
5. Acessar `http://localhost:8080`.



## 15. Segue um procedimento seguro para corrigir os erros de permissão no projeto Yii2/Docker.

Procedimento
1. Rodar correção geral no container PHP
docker exec -u root trotesolidario-php-1 bash -lc "
mkdir -p \
  /var/www/html/web/assets \
  /var/www/html/runtime/mpdf/mpdf \
  /var/www/html/web/pdf/certificados

chown -R www-data:www-data \
  /var/www/html/runtime \
  /var/www/html/web/assets \
  /var/www/html/web/pdf

chmod -R 775 \
  /var/www/html/runtime \
  /var/www/html/web/assets \
  /var/www/html/web/pdf
"
2. Reiniciar os containers
docker compose restart