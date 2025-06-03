# Makefile

.PHONY: reset-db

reset-db:
	@echo "Dropping the database..."
	php bin/console doctrine:database:drop --force --if-exists

	@echo "Creating the database..."
	php bin/console doctrine:database:create

	@echo "Running migrations..."
	#php bin/console doctrine:migrations:migrate --no-interaction

	@echo "Updating schema..."
	php bin/console doctrine:schema:update --force

	@echo "Loading fixtures..."
	php bin/console doctrine:fixtures:load --no-interaction

clean:
	php bin/console cache:clear
	php bin/console cache:warm
	./vendor/bin/php-cs-fixer fix