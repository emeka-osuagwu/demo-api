
### Install package
- docker exec -it sabinus_service_api composer require example-package-name

### Runing Test
- docker exec -it sabinus_service_api php artisan test --filter Name_Of_Test

### Runing Logs
- docker exec -it sabinus_service_api php artisan tail

### Run migration and seed
- docker exec -it sabinus_service_api php artisan bigquery_db:seed

### Run bigquery migration
- docker exec -it sabinus_service_api php artisan bigquery_db:migrate

### Clear config
docker exec -it sabinus_service_api php artisan config:clear && docker exec -it sabinus_service_api php artisan cache:clear && docker exec -it sabinus_service_api php artisan optimize:clear && docker exec -it sabinus_service_api composer dumpautoload



docker exec -it sabinus_service_api composer require google/cloud-bigquery

Naming Convention

## transaction

<!-- - get transaction by player_id -->

## Games

<!-- - change word schema to dictionary -->
<!-- - create a dictionary (dictionary.create) -->
<!-- - bulk upload a dictionary (dictionary.upload) -->
<!-- - fetch all dictionary (dictionary.fetchAllEntry) -->
<!-- - delete a dictionary (dictionary.deleteEntryById) -->
  