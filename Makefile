#include ./api/.env

start:
	docker-compose up -d

stop:
	docker-compose down

artisan:
	docker-compose exec api php artisan $(filter-out $@,$(MAKECMDGOALS))

#db-import-dump:
#	docker-compose exec -T mysql mysql --user=${DB_USERNAME} --password=${DB_PASSWORD} ${DB_DATABASE} < ${path}