# Como acessar o container MySQL

Execute os comandos na raiz do projeto, onde está localizado o arquivo `docker-compose.yml`.

## Abrir o terminal do container

```bash
docker compose exec mysql-db bash
```

Em seguida, conecte-se ao banco usando as variáveis configuradas no `.env`:

```bash
mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"
```

## Conectar diretamente

Para conectar sem abrir primeiro o terminal do container:

```bash
docker compose exec mysql-db sh -lc 'mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"'
```

## Comandos úteis

```sql
SHOW TABLES;

DESCRIBE tipo_categoria_custo;

DESCRIBE categoria_custo;

DESCRIBE distribuicao_custo;

SELECT * FROM tipo_categoria_custo;

SELECT * FROM categoria_custo;

SELECT * FROM distribuicao_custo;

EXIT;
```

## Sair do container

Se você abriu o terminal do container com `bash`, execute:

```bash
exit
```

> Não escreva a senha diretamente na documentação ou em comandos versionados. Utilize as variáveis do `.env`.
