#!/usr/bin/env bash

# Install Docker
if which docker > /dev/null 2>&1; then
    echo 'Docker has already installed'
else
    sudo yum update -y \
        && yum install -y docker \
        && service docker start \
        && usermod -a -G docker ec2-user
fi

# Install Docker-Compose
if which docker-compose > /dev/null 2>&1; then
    echo 'Docker-Compose has already installed'
else
    sudo curl -L "https://github.com/docker/compose/releases/download/1.22.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose \
        && chmod +x /usr/local/bin/docker-compose
fi

echo 'Exit EC2 and re-login to enable docker'