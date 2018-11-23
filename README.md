Framework: Laravel(5.7)

Docker Images: Nginx, php-fpm, MySQL, Redis

# How to set up
supposed to be used on EC2

```
$ git clone https://github.com/KushibikiMashu/hiphop_station.git
$ cd hiphop_station
$ ./start.first.sh
$ ./start.second.sh
$ docker-compose up -d
```

Set React in Laravel
```
$ cd laravel
$ composer install
$ npm i
$ npm run prod
```

Set your Youtube API key in the .env file

```
YOUTUBE_API_KEY = KEY
```

Don't forget add this code in the file using alaouy/Youtube API

```
use Alaouy\Youtube\Facades\Youtube;
```

see details below
https://github.com/alaouy/Youtube

## composer
If you added a new folder under app directory, write the directory name in composer.json

```
   "autoload": {
        "classmap": [
            "database/seeds",
            "database/factories",
            "app/Repositories",
            "app/Services"  /* here */
        ],
        ...
   }
```

Update composer autoload

```
$ composer dump-autoload
```
