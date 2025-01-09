init: docker-down-clear docker-pull docker-build docker-up api-init
up: docker-up
down: docker-down
restart: down up
pull: docker-pull
build: docker-build

docker-up:
	docker-compose up -d
docker-down:
	docker-compose down --remove-orphans
docker-down-clear:
	docker-compose down -v --remove-orphans
docker-pull:
	docker-compose pull
docker-build:
	docker-compose build --pull

api-init: api-composer-install api-permissions api-wait-db api-migrations api-fixtures
test: api-test
lint: api-lint
check: lint analyze validate-schema test
fix: api-fix
analyze: api-analyze

api-test:
	docker-compose run --rm api-php-cli composer test
api-fix:
	docker-compose run --rm api-php-cli composer php-cs-fixer fix
api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/*'

api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30
api-migrations:
	docker-compose run --rm api-php-cli composer app migrations:migrate
api-fixtures:
	docker-compose run --rm api-php-cli composer app fixtures:load
api-validate-schema:
	docker-compose run --rm api-php-cli composer app orm:validate-schema
api-composer-install:
	docker-compose run --rm api-php-cli composer install
api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli composer php-cs-fixer fix -- --dry-run --diff
api-analyze:
	docker-compose run --rm api-php-cli composer psalm
api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 var

build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/slim_service-gateway:${IMAGE_TAG} gateway/docker
build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/slim_service-frontend:${IMAGE_TAG} frontend
build-api:
	docker --log-level=debug build --pull --file=api/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/slim_service-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/prod/php-fpm/Dockerfile --tag=${REGISTRY}/slim_service-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/prod/php-cli/Dockerfile --tag=${REGISTRY}/slim_service-api-php-cli:${IMAGE_TAG} api
try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/slim_service-gateway:${IMAGE_TAG}
push-frontend:
	docker push ${REGISTRY}/slim_service-frontend:${IMAGE_TAG}
push-api:
	docker push ${REGISTRY}/slim_service-api:${IMAGE_TAG}
	docker push ${REGISTRY}/slim_service-api-php-fpm:${IMAGE_TAG}

deploy:
	ssh ${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh ${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	scp -P ${PORT} docker-compose-prod.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=slim_service" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_DB_PASSWORD=${API_DB_PASSWORD}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build -d api-postgres'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 60'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run --rm api-php-cli php bin/app.php migrations:migrate --no-interaction'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

rollback:
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'