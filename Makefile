.PHONY: install bench
.DEFAULT_GOAL := help

install:
	composer install --no-interaction --no-progress --no-suggest --optimize-autoloader

bench: composer.lock
	php vendor/bin/phpbench run --report=aggregate
	php vendor/bin/phpbench run --report=aggregate --revs=100
	php vendor/bin/phpbench run --report=aggregate --revs=10000
	php vendor/bin/phpbench run --report=aggregate --iterations=100
	php vendor/bin/phpbench run --report=aggregate --revs=100 --iterations=100
