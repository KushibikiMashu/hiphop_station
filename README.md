Framework: Laravel(5.7)

Docker Images: Nginx, php-fpm, Redis

# How to set up

```
$ git clone https://github.com/KushibikiMashu/hiphop_station.git
$ cd hiphop_station

$ sudo chmod -R 777 laravel/storage && sudo chmod -R 777 laravel/bootstrap
```

Install predis to Laravel
```
$ cd laravel
$ composer require predis/predis
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