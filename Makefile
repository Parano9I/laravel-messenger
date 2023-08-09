#include ./api/.env

start:
	docker-compose up -d

stop:
	docker-compose down

artisan:
	docker-compose exec api php artisan $(filter-out $@,$(MAKECMDGOALS))

sync-node-modules:
	docker-compose cp frontend:/var/www/node_modules/ frontend/

#db-import-dump:
#	docker-compose exec -T mysql mysql --user=${DB_USERNAME} --password=${DB_PASSWORD} ${DB_DATABASE} < ${path}