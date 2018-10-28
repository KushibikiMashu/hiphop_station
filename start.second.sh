#!/usr/bin/env bash

MY_IP=`curl inet-ip.info`

if [ "${MY_IP}" = "106.161.242.135" ]; then
    # Local Environment
    ENV_NAME='local'
else
    # PROD Environment
    ENV_NAME='prod'
fi

# Set up Laravel
cd laravel \
     && cp .env.${ENV_NAME} .env \
     && sudo chmod -R 777 storage \
     && sudo chmod -R 777 bootstrap