# OpenAPI Client Runtime

[![Latest Stable Version](https://poser.pugx.org/allansun/openapi-runtime/v)](https:://packagist.org/packages/allansun/openapi-runtime)
[![Total Downloads](https://poser.pugx.org/allansun/openapi-runtime/downloads)](https:://packagist.org/packages/allansun/openapi-runtime)
[![License](https://poser.pugx.org/allansun/openapi-runtime/license)](https:://packagist.org/packages/allansun/openapi-runtime)
[![codecov](https://codecov.io/gh/allansun/openapi-runtime/branch/master/graph/badge.svg?token=OVYRXPSW2Y)](https://codecov.io/gh/allansun/openapi-runtime)


Runtime library to be used with other SDK generated from OpenAPI docs.

## Installation

```shell
composer require allansun/openapi-runtime
```

You will also need a [PSR-7 based client](https://www.php-fig.org/psr/psr-7/)
or [Symfony's HTTP Foundation based client](https://symfony.com/doc/current/http_client.html)

So either use Guzzle (or any other PSR-7 compatible clients)

```shell
composer require guzzlehttp/guzzle
```

**or** Symfony HTTP Client

```shell
composer require symfony/http-client
```

## Basic concepts

OpenAPI (formally Swagger) defines how API should be interacted with by giving

- API endpoint URI
- input payload and output response (normally in JSON format)

Code generators can generate valid code by parsing the OpenAPI doc.

We try to provide a guidance on how you should organize your code generation.

### Transportation client

Under the hood this `Client` depends on some transportation libraries to communicate with API endpoints.

At the moment we support [PSR-7 based client](https://www.php-fig.org/psr/psr-7/)
and [Symfony's HTTP Foundation based clients](https://symfony.com/doc/current/http_client.html).

To use the `Client` you first need to use Client::configure() method to define the transportation client of your choice.

### ResponseHandlers

One key function this lib provides is to transform response JSON into predefined PHP objects.

By calling Client::configure() you can customize your own [ResponseHanderStack](/src/ResponseHandlerStack.php) , which
basically is a stack of transformers to parse the response JSON.

You can create your own ResponseHandler by implementing the
[ResponseHandlerInterface](/src/ResponseHandler/ResponseHandlerInterface.php). It is simply an invokable class either
returns a [Model](/src/ModelInterface.php) or throws an
[UndefinedResponseException](/src/ResponseHandler/Exception/UndefinedResponseException.php) so the ResponseHandlerStack
can try next handler.

By default, we provide a simple JSON response handler(
[JsonPsrResponseHandler](/src/ResponseHandler/JsonPsrResponseHandler.php) or
[JsonSymfonyResponseHandler](/src/ResponseHandler/JsonSymfonyResponseHandler.php)). They both will try to parse the
response into a model by looking into reference, which is defined in [ReponseTypes](/src/ResponseTypes.php). Be aware
you should set up your response references by calling ResponseTypes::setTypes(). For example on how to use it please
look [ClientTest](/tests/ClientTest.php)

## Usage

First your generated code should provide a way to parse response references into ResponseTypes

```php
<?php 
namespace App;

use OpenAPI\Runtime\ResponseTypes;

ResponseTypes::setTypes([
    'operation_id' =>[ // This should be unique to $ref as defined in the OpenAPI doc
        '200.' => 'YourGeneratedModelClass::class', // We add a dot after there HTTP status code to enforce string tyep
        '404.' => 'ErrorModel::class'
    ]
]);  
```

Next you need to configure the transportation client and respones handerls

```php
<?php
namespace App;

use OpenAPI\Runtime\Client;
use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\SimplePsrResponseHandlerStack;

Client::configure(
    new \GuzzleHttp\Client([
        'base_uri' => 'https://pastebin.com/'
    ]),
    new SimplePsrResponseHandlerStack(new ResponseTypes()),
    [] // Put some extra default options here
);
```

Then in the generated code you can call the Client::request() method to your api end points

```php
<?php
// Should be generated code here
namespace App\GeneratedCode;

use OpenAPI\Runtime\AbstractAPI;

class Customer extends AbstractAPI{
    public function get($id)
    {
        return $this->client->request('operation_id', 'GET',"/customer/${id}",null);
    }
    
    public function post($payload)
    {
        return $this->client->request('post_operation_id','POST','customer',[
            'json' => $payload
        ]);
    }
}
```

## Projects using this lib

- [allansun/openapi-code-generator](https://github.com/allansun/openapi-code-generator)
- [bricre/royalmail-tracking-v2-sdk](https://gihub.com/bricre/royalmail-tracking-v2-sdk)
- [kubernetes/php-client](https://packagist.org/packages/kubernetes/php-client)
