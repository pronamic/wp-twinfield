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

cd $(wp-env install-path)

if [[ $(docker-compose exec -T -u root wordpress php -m | grep soap) != "soap" ]]; then
    echo "Installing: SOAP Extension."
    docker-compose exec -T -u root wordpress apt-get update
    docker-compose exec -T -u root wordpress apt-get install -y libxml2-dev
    docker-compose exec -T -u root wordpress docker-php-ext-install soap
    docker-compose exec -T -u root wordpress service apache2 reload
    echo "SOAP Extension: Installed."
fi
