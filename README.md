# exphpress

Exphpress (pronounced as 'exfress') is a PHP clone of the famous Node.js framework, Express. The goal is a 80% api compatibility with it.

It's just a hobby project for fun, but it can be useful if you need your well-known Express.js and async scalability.



## Supported API calls

Node.js: 

```javascript
const express = require('express')
const app = express()
const port = 3000

app.get('/', (req, res) => res.send('Hello World!'))

app.use(function (req, res, next) {
  console.log('Example middleware')
  next()
})

app.listen(port, () => console.log(`Example app listening on port ${port}!`))
```

and the same in PHP:

```php
$app = new \Exphpress\App();
$port = 8080;

$app->get('/hello', function (ServerRequestInterface $request) {
    error_log("handler", 4);
    return new Response(
        200,
        array(
            'Content-Type' => 'text/plain'
        ),
        'Hello World!'
    );
});

$app->use(function (ServerRequestInterface $request, callable $next) {
    error_log("example middleware");
    return $next($request);
});

$app->listen($port);
```

## Run / Install

```bash
composer require balintsera/exphpress
```

Then organize your project anyway, the framework is located in this package.

Because it has some special needs, like libuv and a pecl extension that wraps libuv, I recommend to use this dockerfile to build and run the example above.

```Dockerfile
FROM php:7.4-cli
COPY . /usr/src/app
WORKDIR /usr/src/app
RUN apt-get update \
    && apt-get install -y libuv1-dev

RUN pecl install uv-0.2.4 \
    && docker-php-ext-enable uv

# install and run composer 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev

CMD [ "php", "./app.php" ]
```

You can utilize this for development too with docker-compose: 

```YAML
version: "3"
services:
  exphpress:
    build: .
    ports: 
      - 8080:8080
    volumes:
    - ./app.php:/usr/src/app/app.php 
```

If you share the source of your app as volumes, you won't need to rebuild the image on every change (but the container still needs to be restarted).


All credits goes to the people who wrote reactPHP and all the other dependencies. Kudos.