# MoreApp PHP Starter Kit

An example project to help MoreApp partners with their first usage of the API.

## Run

```
composer install
composer dump-autoload -o
php index.php
```

## Example code

Check out the file `index.php` to see interaction with the API.

The example code starts with defining configuration.
It sets up a client with Oauth1-middleware using the configuration.

```
$this->client = new Client([
    'base_uri' => $this->endpoint,
    'handler' => $stack,
    'auth' => 'oauth'
]);
```

The client can be used to make an authorized call to the MoreApp API. The example code will fetch all customers for the authorized partner.

```
$response = $this->client->get('customers');
$customers = json_decode($response->getBody()->getContents());
```

Finaly the code will output the result.

```
print_r($customers);
```

## Changing the example for your own usage

To use the example for your own usage change the configuration in `index.php`.

- The `endpoint` should be `https://api.moreapp.com/api/v1.0`. For the example we use the MoreApp develop environment. Please do not use this.
- The `consumer_key` should be replaced with your MoreApp username (email address) or partner username
- The `consumer_secret` should be replaced with your own consumer_secret, that can be generated at <a href="https://docs.moreapp.com/#/api/authentication">https://docs.moreapp.com/#/api/authentication -> Generating the signature</a>
