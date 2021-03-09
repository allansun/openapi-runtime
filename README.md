# OpenAPI Client Runtime

Runtime library to be used with other SDK generated from OpenAPI docs.

## Basic concepts

OpenAPI (formally Swagger) defines how API should be interacted with by giving 

 - uri
 - input payload and output response (normally in JSON format)

Code generators can generate valid code by parsing the OpenAPI doc.

We try to provide a guidance on how you should organize your code generation.

### Transportation client

Under the hood this `Client` depends on some transportation libraries to communicate with API endpoints.

At the moment we support [PSR-7 based client](https://www.php-fig.org/psr/psr-7/) and [Symfony's HTTP Foundation based 
clients](https://symfony.com/doc/current/http_client.html).

To use the `Client` you first need to use Client::configure() method to define the transportation client of your 
choice. You can call Client::configure() without any parameters to create a default 
[GuzzleClient](https://docs.guzzlephp.org/en/stable/psr7.html), however it is strongly advised to configure your own 
transportation for things such as `base_uri` or authentication credentials.

### ResponseHandlers
One key function this lib provides is to transform response JSON into predefined PHP objects.

By calling Client::configure() you can create your own [ResponseHanderStack](/src/ResponseHandlerStack.php) , which 
essetially a stack a transformers try to parse the response JSON.

You can create your own ResponseHandler by implementing the 
[ResponseHandlerInterface](/src/ResponseHandler/ResponseHandlerInterface.php). It is simple an invokable class 
either returns a [Model](/src/ModelInterface.php) or throws an 
[UndefinedResponseException](/src/ResponseHandler/Exception/UndefinedResponseException.php) so the 
ResponseHandlerStack can try next handler.

By default, we provide a simple JSON response handler(
[JsonPsrResponseHandler](/src/ResponseHandler/JsonPsrResponseHandler.php) or
[JsonSymfonyResponseHandler](/src/ResponseHandler/JsonSymfonyResponseHandler.php)). They both will try to parse the 
response into a model by looking into reference, which is defined in [ReponseTypes](/src/ResponseTypes.php). Be 
aware you should set up your response references by calling ResponseTypes::setTypes(). For example please look in 
[ClientTest](/tests/ClientTest.php)

## Usage

First your generated code should provide a way to parse response references into ResponseTypes

```php
<?php 
namespace App;

use OpenApiRuntime\ResponseTypes;

ResponseTypes::setTypes([
    'operation_id' =>[ // This is should be unique to $ref as defined in the OpenAPI doc
        '200.' => 'YourGeneratedModelClass::class', // We add a dot after there HTTP status code to enforce string tyep
        '404.' => 'ErrorModel::class'
    ]
]);  
```

Next you need to configure the transportation client and respones handerls

```php
<?php
namespace App;

use OpenApiRuntime\Client;
use OpenApiRuntime\DefaultResponseHandlerStack;

Client::configure(
    new \GuzzleHttp\Client([
        'base_uri' => 'https://pastebin.com/'
    ]),
    new DefaultResponseHandlerStack(),
    [] // Put some extra default options here
);
```

Then in the generated code you can call the Client::request() method to your api end points

```php
<?php
// Should be generated code here
namespace App\GeneratedCode;

use OpenApiRuntime\AbstractAPI;

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

 - [kubernetes/php-client](https://packagist.org/packages/kubernetes/php-client)
