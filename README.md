Framework: Laravel(5.7)

Docker Images: Nginx, php-fpm, Redis

# How to set up

```
$ git clone https://github.com/KushibikiMashu/hiphop_station.git
$ cd hiphop_station

$ sudo chmod -R 777 laravel/storage && sudo chmod -R 777 laravel/bootstrap
```

Install predis and React to Laravel
```
$ cd laravel
$ composer require predis/predis
$ php artisan preset react
$ npm install
$ npm i -g npm to update
```

Don't forget to use customized .env file.

```
$ cp .env.local .env
```

```
$ cd ..
$ docker-compose up -d
```

Well done!

# install alaouy/Youtube

see URL below

https://github.com/alaouy/Youtube

Configuration
In /config/app.php add YoutubeServiceProvider:

```
Alaouy\Youtube\YoutubeServiceProvider::class,
```

Do not forget to add also Youtube facade there:

```
'Youtube' => Alaouy\Youtube\Facades\Youtube::class,
```

Publish config settings:

```
$ php artisan vendor:publish --provider="Alaouy\Youtube\YoutubeServiceProvider"
```

Set your Youtube API key in the .env file

```
YOUTUBE_API_KEY = KEY
```

Don't forget add this code in the file using alaouy/Youtube API

```
use Alaouy\Youtube\Facades\Youtube;
```

install Material UI

```
$ npm install @material-ui/core
$ npm install @material-ui/icons
$ npm install --save-dev @babel/preset-es2016
```

Add Laravel task schedule to crontab

```
$ vi /etc/crontabs/laravel_schedule
* * * * * cd /var/www/laravel && php artisan schedule:run >> /dev/null 2>&1
```