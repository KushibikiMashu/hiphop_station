Framework: Laravel(5.7)
Docker Images: Nginx, php-fpm, Redis

# How to set up

```
$ git clone https://github.com/KushibikiMashu/hiphop_station.git
$ cd hiphop_station

$ chmod -R 777 laravel/storage
$ chmod -R 777 laravel/bootstrap
```

Install predis to Laravel
```
$ cd laravel
$ composer require predis/predis
```

```
$ cd ..
$ docker-compose up -d
```

Well done!
