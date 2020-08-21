Laravel-RocketShipIt
====================

Laravel-RocketShipIt is a simple package that you can include in a Laravel project to allow you
to make requests to either a RocketShipIt binary locally, a self-hosted api endpoint, or the endpoint
maintained by RocketShipIt.

## Requirements

- The RocketShipIt binary OR a RocketShipItLicense to use the API. [More Info](https://rocketship.it)
- If using the RocketShipIt binary, the licence file must reside in the same directory as the binary.

## Installation

Require this package with composer using the following command:
```
comoser require doubleoh13/laravel-rocketshipit
```

The package will automatically register is's service provider.
You can optionally publish the config file with:

```
php artisan vendor:publish --provider="DoubleOh13\RocketShipIt\RocketShipItServiceProvider"
```

## Configuration

To use the binary, set its location in your ```.env``` file:
```
ROCKETSHIPIT_BINARY_LOCATION=/path/to/RocketShipIt
```
Ensure that the file is executable and the license file is in the same directory.

To use the API, set the following parameters. *(The Endpoint is only required if you are self-hosting)*:
```
ROCKETSHIPIT_API_KEY=[api key]
ROCKETSHIPIT_ENDPOINT=https://api.rocketship.it/v1/
```

## Usage

Sending a request array to the request method will return a stdClass Object.

For details about request parameters and response data, see the [RocketShipIt Documentation](https://www.rocketship.it/support).
```
// In Controller.php
use DoubleOh13\RocketShipIt\Client

public function index(Client $client)
{
    $response = $client->request(
        [
            'carrier' => 'stamps',
            'action' => 'AccountInfo',
            'params' => [
                'username' => 'username',
                'password' => 'password',
            ]
        ]
    )
}
```

```
// Using the Facade

public function index()
{
    $response = \RocketShipIt::request(
            [
                'carrier' => 'stamps',
                'action' => 'AccountInfo',
                'params' => [
                    'username' => 'username',
                    'password' => 'password',
                ]
            ]
        )
{
```

### Other Methods

When you call the `RocketShipIt::request()` method, it will attempt to use the binary file, when it
exists, and the license is present, otherwise it will use the API method. If you prefer to call one
or the other method directly, there is also the `RocketShipIt::httpRequest()` for API requests and the 
`RocketShipIt::binRequest()` for binary requests.
