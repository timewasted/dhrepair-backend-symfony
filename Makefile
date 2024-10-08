# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh bash composer vendor sf cc init-test-db test test-coverage test-functional test-unit check check-code fix-code pre-commit

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

restart: down up ## Restart the containers

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

init-test-db: ## Initialize the test database by importing the schema and creating fixtures
	@$(SYMFONY) doctrine:query:sql --env=test -- 'DROP DATABASE app_test; CREATE DATABASE app_test;'
	@$(SYMFONY) doctrine:schema:create --env=test
	@$(SYMFONY) doctrine:fixtures:load --env=test --no-interaction --no-debug --purge-with-truncate --purger mysql_purger

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

test-coverage: ## Start tests with phpunit and generate a coverage report, pass the parameter "c=" to add options to phpunit
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test -e XDEBUG_MODE=coverage php bin/phpunit --coverage-html .phpunit.coverage $(c)

test-functional: c=tests/functional ## Perform only functional tests
test-functional: test

test-unit: c=tests/unit ## Perform only unit tests
test-unit: test

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— Code Checks ——————————————————————————————————————————————————————————————
check: check-code ## Check the project for potential issues

check-code: ## Check the code with psalm
	@$(PHP_CONT) vendor/bin/psalm --config=/app/psalm.xml

fix-code: ## Run php-cs-fixer
	@$(PHP_CONT) vendor/bin/php-cs-fixer fix

pre-commit: check-code fix-code test ## Perform checks required before committing code
