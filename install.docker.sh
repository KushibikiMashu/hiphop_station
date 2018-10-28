#!/usr/bin/env bash

# Install Docker
if which docker > /dev/null 2>&1; then
    echo 'Docker has already installed'
else
    sudo yum update -y
    sudo yum install -y docker
    sudo service docker start
    sudo usermod -a -G docker ec2-user
fi

# Install Docker-Compose
if which docker-compose > /dev/null 2>&1; then
    echo 'Docker-Compose has already installed'
else
    sudo curl -L "https://github.com/docker/compose/releases/download/1.22.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
fi