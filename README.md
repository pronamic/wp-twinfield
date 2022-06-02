# Twinfield library for WordPress

[![Build Status](https://travis-ci.org/wp-twinfield/twinfield.svg)](https://travis-ci.org/wp-twinfield/twinfield)
[![Coverage Status](https://coveralls.io/repos/wp-twinfield/twinfield/badge.svg?branch=develop&service=github)](https://coveralls.io/github/wp-twinfield/twinfield?branch=develop)

## Documentation

*	[Twinfield API](https://www.twinfield.nl/api)


## Examples

See the [`examples/`](examples) directory for examples of the key client features. You can
view them in your browser by running the php built-in web server.

```
$ php -S localhost:8000 -t examples/
```

And then browsing to the host and port you specified
(in the above example, `http://localhost:8000`).

## Environment variables

```
export TWINFIELD_USER=
export TWINFIELD_PASSWORD=
export TWINFIELD_ORGANISATION=
export TWINFIELD_OFFICE_CODE=
export TWINFIELD_ARTICLE_CODE=
export TWINFIELD_SUBARTICLE_CODE=
export TWINFIELD_CUSTOMER_CODE=
export TWINFIELD_SALES_INVOICE_TYPE=
export TWINFIELD_SALES_INVOICE_NUMBER=
```


## Travis CI

```
travis encrypt TWINFIELD_USER=$TWINFIELD_USER --add env.global
travis encrypt TWINFIELD_PASSWORD=$TWINFIELD_PASSWORD --add env.global
travis encrypt TWINFIELD_ORGANISATION=$TWINFIELD_ORGANISATION --add env.global
travis encrypt TWINFIELD_OFFICE_CODE=$TWINFIELD_OFFICE_CODE --add env.global
travis encrypt TWINFIELD_ARTICLE_CODE=$TWINFIELD_ARTICLE_CODE --add env.global
travis encrypt TWINFIELD_CUSTOMER_CODE=$TWINFIELD_CUSTOMER_CODE --add env.global
travis encrypt TWINFIELD_SALES_INVOICE_TYPE=$TWINFIELD_SALES_INVOICE_TYPE --add env.global
travis encrypt TWINFIELD_SALES_INVOICE_NUMBER=$TWINFIELD_SALES_INVOICE_NUMBER --add env.global
```


## Debug PHP SOAP

```php
var_dump( $this->get_wsdl_url() );
var_dump( $this->soap_client->__getFunctions() );
var_dump( $this->soap_client->__getTypes() );

try {
	$test = $this->soap_client->Query( $test );
} catch ( \Exception $e ) {
	var_dump( $e );
}

echo "REQUEST:\n" . $this->soap_client->__getLastRequest() . "\n";
```

## Plugin

```
ln -s ~/Projects/wp-twinfield-new  ~/Local\ Sites/twinfield/app/public/wp-content/plugins/pronamic-twinfield
```

## To-do

```php
$client = new Client(
	$client_id,
	$client_secret,
	$redirect_uri
);

$client->set_access_token( ... );

if ( $client->is_access_token_expired() ) {
	
}

$authentication = {}

$authentication->get_user();

$authentication->get_user()->get_organisation();

$authentication->get_organisation();

$authentication->get_cluster_url();

$authentication->get_organisation()->get_offices();

$client = $authentication->get_client();

$client->get_organisation_service();

$offices = $organisation_service->get_offices();
```

## Inspiration

*	https://github.com/KnpLabs/php-github-api
*	https://github.com/J7mbo/twitter-api-php
*	https://github.com/galen/PHP-Instagram-API
*	https://github.com/drewm/mailchimp-api
*	https://github.com/Happyr/LinkedIn-API-client
*	https://github.com/okwinza/cloudflare-api
*	https://github.com/jcroll/foursquare-api-client
*	https://github.com/googleapis/google-api-php-client
*	https://developers.google.com/gmail/api/quickstart/php


## Design Patterns

*	https://en.wikipedia.org/wiki/Interpreter_pattern


## Architecture

### REST API

```
/wp-json/pronamic-twinfield/v1/user
/wp-json/pronamic-twinfield/v1/organisation
/wp-json/pronamic-twinfield/v1/authentications/1/organisations
/wp-json/pronamic-twinfield/v1/authentications/1/user
/wp-json/pronamic-twinfield/v1/authentications/1/organisation
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc/offices/
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc/offices/66470_A
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc/offices/66470_A/dimension-types
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc/offices/66470_A/dimensions/DEB/
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc/offices/66470_A/dimensions/DEB/1002
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc/offices/66470_A/declarations
/wp-json/pronamic-twinfield/v1/organisations/vandermeeracc/offices/66470_A/periods
```

### Web

```
/twinfield/user
/twinfield/organisation
/twinfield/organisations
/twinfield/organisations/vandermeeracc
/twinfield/organisations/vandermeeracc/offices
/twinfield/organisations/vandermeeracc/offices/66470_A
/twinfield/organisations/vandermeeracc/offices/66470_A/dimension-types
/twinfield/organisations/vandermeeracc/offices/66470_A/declarations
/twinfield/organisations/vandermeeracc/offices/66470_A/periods

/twinfield/... » /wp-json/pronamic-twinfield/v1/...
```

### CLI

```
wp twinfield user
wp twinfield organisation
wp twinfield ... » /wp-json/pronamic-twinfield/v1/...
wp twinfield bank-statements $( wp twinfield offices list --field=code ) --date_from=2022-01-01 --date_to=2023-01-01 --include_posted=true --pull=true
```

### Config

```php
define( 'PRONAMIC_TWINFIELD_CLIENT_ID', , '' );
define( 'PRONAMIC_TWINFIELD_CLIENT_SECRET', '' );
define( 'PRONAMIC_TWINFIELD_REDIRECT_URI', '' );
```

## Other

*	http://www.accountingcoach.com/blog/accounts-payable-accounts-receivable
*	http://blog.fedecarg.com/2009/03/12/domain-driven-design-and-data-access-strategies/
*	http://stackoverflow.com/questions/3056447/design-pattern-for-loading-multiple-message-types
*	http://www.servicedesignpatterns.com/requestandresponsemanagement/datatransferobject
*	http://stackoverflow.com/questions/29592216/how-should-i-store-metadata-in-an-object
*	https://secure.php.net/soundex

[![Pronamic - Work with us](https://github.com/pronamic/brand-resources/blob/main/banners/pronamic-work-with-us-leaderboard-728x90%404x.png)](https://www.pronamic.eu/contact/)
