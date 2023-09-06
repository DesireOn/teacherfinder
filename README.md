# TeacherFinder

A project, built on Symfony MVC + twig.


To get it working, follow these steps:

Start the containers:

```
docker-compose up -d
```

Enter the app container:

```
docker-compose exec app bash
```

Install composer dependencies:
```
composer install
```

Next (while still in the app container), build the database, execute the migrations and load the fixtures with:

```
# "symfony console" is equivalent to "bin/console"
# but its aware of your database container
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load
```
