PARALLELISM := $(shell nproc)

.PHONY: all
all: install phpcbf phpcs phpstan phpunit infection

.PHONY: install
install: vendor/composer/installed.json

vendor/composer/installed.json: composer.json composer.lock
	@composer install $(INSTALL_FLAGS)
	@touch -c composer.json composer.lock vendor/composer/installed.json

.PHONY: phpunit
phpunit:
	@vendor/bin/phpunit

.PHONY: infection
infection:
	@vendor/bin/phpunit --coverage-xml=build/coverage-xml --log-junit=build/junit.xml $(PHPUNIT_FLAGS)
	@vendor/bin/infection -s --threads=$(PARALLELISM) --coverage=build

.PHONY: phpcbf
phpcbf:
	@vendor/bin/phpcbf --parallel=$(PARALLELISM)

.PHONY: phpcs
phpcs:
	@vendor/bin/phpcs --parallel=$(PARALLELISM) $(PHPCS_FLAGS)

.PHONY: phpstan
phpstan:
	@vendor/bin/phpstan analyse
