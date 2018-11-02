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
$ php artisan preset react
$ npm install
$ npm run prod
```

Add Laravel task schedule to crontab

```
$ vi /etc/crontabs/laravel_schedule
* * * * * cd /var/www/laravel && php artisan schedule:run >> /dev/null 2>&1
```

Set your Youtube API key in the .env file

```
YOUTUBE_API_KEY = KEY
```

Don't forget add this code in the file using alaouy/Youtube API

```
use Alaouy\Youtube\Facades\Youtube;
```

see URL below
https://github.com/alaouy/Youtube
