#!/bin/bash -x

if [ "$ENV" == "local" ] || [ "$ENV" == "test" ]; then
    pecl install xdebug
    mv /tmp/xdebug.ini /usr/local/etc/php/conf.d/
else
    rm /tmp/xdebug.ini
fi
