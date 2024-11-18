# Project Structure: onion

Песочница для проверки различных гипотез в области организации структуры проекта.

Требования:
- приложение - коноль, несколько методов для чтения и записи данных
- данные модели хранятся удалённо, чтение и запись API / gRPC

Легенда. Виртуальная книжная полка, основные действия:
- загрузить на полку книгу (книги хранятся в удалённом репозитории, доступ через API)
- получить книгу с полки
- у книги есть Автор, связь один к одному (данные хранятся в локальном репозитории)

Структура:
- **application**
  Слой первичной валидации и подготовки конечного ответа.
- **domain**
  Основная бизнес логика.
- **infrastructure**
  Библиотеки организующие связь между доменом и инфраструкторой.
- **presentation**
  Точка входа: API / console / etc.

### Вопросики

- можно ли один и тот же VO использовать для различных Entity, или корректнее заводить персональный объект?
- где создавать агрегаты, как организовать связи между доменными моделями - в доменном сервисе? на уровне приложения?

### Плюсы

+ domain ничего не знает об application, что отлично инкапсулирует логику от окружения;
+ infrastructure так же в достаточной степени изолирована от application, через интерфейсы;

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

```shell
make phpstan
```

```shell
make check
```

### Code Sniffer

```shell
make phpcs
```

### Rector

```shell
make rector
```

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
make phpunit
```
**phpunit**, чтобы перейти на 11 версию, нужно отказываться от psalm,
который цепляется за четвертую версию парсера от Никиты Попова (https://github.com/nikic/PHP-Parser).
Плавно переезжаем на phpstan?.

