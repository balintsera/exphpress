#!/bin/bash

docker run --rm --name exphpress -v "$PWD":/usr/src/myapp -w /usr/src/myapp -p 8080:8080 php:7.4-cli php server.php