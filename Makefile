start:
	./vendor/bin/sail up

stop:
	./vendor/bin/sail down

test:
	docker exec -t ada-api-laravel.test-1 ./vendor/bin/pest
