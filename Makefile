PHP_VERSION ?= 8.3
USER = $$(id -u)

# https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
.PHONY: help
.DEFAULT_GOAL := help

help: ## Display this help screen
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

composer: ## composer install
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		composer:latest \
		composer install --optimize-autoloader

composer-up: ## composer update
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		composer:latest \
		composer update --no-cache

composer-dump:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		composer:latest \
		composer dump-autoload

# https://php.watch/articles/composer-audit#composer-audit
composer-audit:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		composer:latest \
		composer audit

# https://github.com/ergebnis/composer-normalize
composer-normalize:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		composer:latest \
		composer normalize

psalm: ## psalm
	docker run --init -it --rm -v "$$(pwd):/app" -u ${USER} -e XDG_CACHE_HOME=/tmp -w /app \
		jakzal/phpqa:php${PHP_VERSION} \
		php ./tools/psalm/vendor/bin/psalm --php-version=${PHP_VERSION}

phpstan: ## phpstan
	docker run --init -it --rm -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/phpstan analyse -c phpstan.neon

phpunit: ## phpunit
	docker run --init -it --rm -v "$$(pwd):/app" -u ${USER} -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/phpunit

phpcs: ## php code snifferphp: detect violations of a defined coding standard
	docker run --init -it --rm -v "$$(pwd):/app" -u ${USER} -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/phpcs

phpcbf: ## php code sniffer: automatically correct
	docker run --init -it --rm -v "$$(pwd):/app" -u ${USER} -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/phpcbf

rector: ## rector
	docker run --init -it --rm -v "$$(pwd):/app" -u ${USER} -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		./vendor/bin/rector

phpunit-coverage:
	docker run --init -it --rm -v "$$(pwd):/app" -u ${USER} -w /app \
		jakzal/phpqa:php${PHP_VERSION} \
		php -d pcov.enabled=1 ./vendor/bin/phpunit --coverage-text

infection:
	docker run --init -it --rm -v "$$(pwd):/app" -u ${USER} -w /app \
		jakzal/phpqa:php${PHP_VERSION} \
		/tools/infection run --initial-tests-php-options='-dpcov.enabled=1'

check: ## detect violations of a defined coding standard and run tests
	-make phpcs
	-make psalm
	-make phpstan
	-make phpunit

auto-repair: ## automatically correct
	-make phpcbf
	-make rector

setup:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/setup/main.php

run:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php

run-author:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php author:view '422b5368-fab4-491b-b252-13b5bb2ec900'

run-author-failure:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php author:view '8cabc407-a3f0-41b3-8f53-b5f1edcff4f0'

run-author-create:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php author:create --name="Vaughn vernon"

run-book:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php book:view '0134434994'

run-book-find:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php book:find --title="Domain-Driven Design Distilled" --author="Vaughn Vernon"

run-book-find-2:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php book:find --title="Domain-Driven Design" --author="Eric Evans"

run-book-import:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php book:import --title="Domain-Driven Design Distilled" --author="Vaughn Vernon"

run-book-import-2:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php book:import --title="Domain-Driven Design" --author="eric evans"

run-book-import-3:
	docker run --init -it --rm -u ${USER} -v "$$(pwd):/app" -w /app \
		ghcr.io/kuaukutsu/php:${PHP_VERSION}-cli \
		php ./src/presentation/cli/main.php book:import --title="Clean Architecture" --author="Robert Martin"
