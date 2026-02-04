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

install_soap() {
    local container="$1"
    local reload_server=${2:-false}
    
    if ! docker-compose exec -T -u root "$container" php -m | grep -q -w "soap"; then
        echo "Installing: SOAP Extension in $container container."
        docker-compose exec -T -u root "$container" apt-get update
        docker-compose exec -T -u root "$container" apt-get install -y libxml2-dev
        docker-compose exec -T -u root "$container" docker-php-ext-install soap
        
        if [[ "$reload_server" == true ]]; then
            docker-compose exec -T -u root "$container" service apache2 reload
        fi
        
        echo "SOAP Extension: Installed in $container container."
    fi
}

install_soap "wordpress" true
install_soap "cli"
