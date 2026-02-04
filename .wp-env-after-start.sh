#!/usr/bin/env bash

#
# Install PHP SOAP Extension for wp-env
#
# This script automatically installs the SOAP extension
# when wp-env starts if it's not already present.
#
# @link https://jclabs.co.uk/blog/wp-env-install-php-extension
#

set -euo pipefail

cd "$(wp-env install-path)"

# Install SOAP extension in the web container (Debian-based)
if [[ $(docker-compose exec -T -u root wordpress php -m | grep -w soap) != "soap" ]]; then
    echo "Installing: SOAP Extension in web container."
    docker-compose exec -T -u root wordpress apt-get update
    docker-compose exec -T -u root wordpress apt-get install -y libxml2-dev
    docker-compose exec -T -u root wordpress docker-php-ext-install soap
    docker-compose exec -T -u root wordpress service apache2 reload
    echo "SOAP Extension: Installed in web container."
fi

# Install SOAP extension in the CLI container (Alpine-based)
if [[ $(docker-compose exec -T cli php -m | grep -w soap) != "soap" ]]; then
    echo "Installing: SOAP Extension in CLI container."
    # Detect PHP version
    PHP_VERSION=$(docker-compose exec -T cli php -r "echo PHP_MAJOR_VERSION . PHP_MINOR_VERSION;" | tr -d '\n\r')
    if [[ -z "$PHP_VERSION" ]]; then
        echo "Warning: Could not detect PHP version in CLI container. SOAP extension not installed in CLI container."
    else
        echo "Detected PHP version: $PHP_VERSION"
        if docker-compose exec -T -u root cli apk add --no-cache "php${PHP_VERSION}-soap"; then
            echo "SOAP Extension: Installed in CLI container."
        else
            echo "Warning: Failed to install php${PHP_VERSION}-soap package. SOAP extension not installed in CLI container."
        fi
    fi
fi
