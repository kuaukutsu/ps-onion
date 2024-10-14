# Project Structure: onion

Песочница для проверки различных гипотез в области организации структуры проекта.

## Docker

```shell
docker pull ghcr.io/kuaukutsu/php:8.3-cli
```

Container:
- `ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli` (**default**)
- `jakzal/phpqa:php${PHP_VERSION}`

### Run example

```shell
make run
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
make psalm
```

### Code Sniffer

```shell
make phpcs
```

### Rector

```shell
make rector
```
