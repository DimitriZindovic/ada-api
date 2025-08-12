start:
	./vendor/bin/sail up

stop:
	./vendor/bin/sail down

serve:
	php artisan serve

test:
	docker exec -t ada-api-laravel.test-1 ./vendor/bin/pest

test-local:
	php artisan test
