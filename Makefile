install:
	composer install
	cp --no-clobber .env.example .env || true
	php artisan key:generate
	php artisan migrate:fresh

test:
	php artisan test -vvv

lint:
	composer exec phpcs --verbose 2>/dev/null
lint-fix:
	composer exec phpcbf --verbose 2>/dev/null
analyse:
	composer exec phpstan analyse --verbose -- --memory-limit=-1 2>/dev/null

docs:
	php artisan ide-helper:eloquent
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	php artisan ide-helper:models --no-interaction --write --smart-reset

clear:
	php artisan route:clear
	php artisan view:clear
	php artisan cache:clear
	php artisan config:clear
	php artisan optimize:clear
