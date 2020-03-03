# exphpress

Exphpress (pronounced as 'exfress') is a PHP clone of the famous Node.js framework, Express. The goal is a 80% api compatibility with it.


## Supported API calls

javascript: 

```javascript
const express = require('express')
const app = express()
const port = 3000

app.get('/', (req, res) => res.send('Hello World!'))

app.listen(port, () => console.log(`Example app listening on port ${port}!`))
```

and the same in PHP:

```php
$app = new \Exphpress\App();
$app->get('/hello', function (ServerRequestInterface $request) {
    error_log("handler", 4);
    return new Response(
        200,
        array(
            'Content-Type' => 'text/plain'
        ),
        'hello world'
    );
});
$app->listen(8080);
```