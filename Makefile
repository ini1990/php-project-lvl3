start:
	php artisan serve --host 0.0.0.0

setup:
	composer install
	cp -n .env.example .env|| true
	php artisan key:gen --ansi

test:
	php artisan test

lint:
	composer phpcs

deploy:
	git push heroku
