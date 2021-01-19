# backend

### Copy from _.env.example_ to _.env_
```shell
cp .env.example .env
```

### Edit file .env
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=EDIT_HERE
DB_USERNAME=EDIT_HERE
DB_PASSWORD=EDIT_HERE
```

### Create a database, called _mails_ 

### Install packages
```shell
composer install
```

### Run migrations
```shell
php artisan migrate
```

### Run seeds
```shell
php artisan db:seed
```

### On server
```shell
php -S localhost:8000 -t public
```
