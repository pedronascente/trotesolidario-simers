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

## 9. Alternativa sem dump: migrations e seeds

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

## 10. Acessos e portas

- Aplicacao web: `http://localhost:8080`
- MySQL pela maquina host: `127.0.0.1:3309`
- MySQL entre containers: `mysql-db:3306`

## 11. Comandos uteis

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

## 12. Fluxo recomendado para ambiente novo

1. Conferir [`.env`](/home/pedro/project/trotesolidario/.env) e [`src/config/db.php`](/home/pedro/project/trotesolidario/src/config/db.php).
2. Subir os containers com `docker compose up -d --build`.
3. Rodar `docker compose run --rm php composer install`.
4. Importar o dump com `docker compose exec -T mysql-db mysql -uroot -ptrote trotesolidario < dump.sql`.
5. Acessar `http://localhost:8080`.
