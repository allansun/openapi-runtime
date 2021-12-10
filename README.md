# OpenAPI Client Runtime

[![Latest Stable Version](https://poser.pugx.org/allansun/openapi-runtime/v)](https://packagist.org/packages/allansun/openapi-runtime)
[![Total Downloads](https://poser.pugx.org/allansun/openapi-runtime/downloads)](https://packagist.org/packages/allansun/openapi-runtime)
[![License](https://poser.pugx.org/allansun/openapi-runtime/license)](https://packagist.org/packages/allansun/openapi-runtime)
[![codecov](https://codecov.io/gh/allansun/openapi-runtime/branch/master/graph/badge.svg?token=OVYRXPSW2Y)](https://codecov.io/gh/allansun/openapi-runtime)


Runtime library to be used with other SDK generated from OpenAPI docs.

## Installation

```shell
composer require allansun/openapi-runtime
```

You will also need a [PSR-18 compatible client](https://www.php-fig.org/psr/psr-18/) see 
[https://docs.php-http.org/en/latest/clients.html](https://docs.php-http.org/en/latest/clients.html) 

So either use Guzzle (or any other PSR-18 compatible clients)

```shell
composer require php-http/guzzle7-adapter
```


## Basic concepts

OpenAPI (formally Swagger) defines how API should be interacted with by giving

- API endpoint URI
- input payload and output response (normally in JSON format)

Code generators can generate valid code by parsing the OpenAPI doc.

We try to provide a guidance on how you should organize your code generation.


### ResponseHandlers

One key function this lib provides is to transform response JSON into predefined PHP objects.

By calling Client::configure() you can customize your own 
[ResponseHanderStack](/src/ResponseHandlerStack/ResponseHandlerStack.php) , which
basically is a stack of transformers to parse the response JSON.

You can create your own ResponseHandler by implementing the
[ResponseHandlerInterface](/src/ResponseHandler/ResponseHandlerInterface.php). It is simply an invokable class either
returns a [Model](/src/ModelInterface.php) or throws an
[UndefinedResponseException](/src/ResponseHandler/Exception/UndefinedResponseException.php) so the ResponseHandlerStack
can try next handler.

By default, we provide a simple JSON response handler(
[JsonPsrResponseHandler](/src/ResponseHandler/JsonResponseHandler.php). It will try to parse the
response into a model by looking into reference, which is defined in [ReponseTypes](/src/ResponseTypes.php). Be aware
you should set up your response references by calling ResponseTypes::setTypes(). 

## Usage

First your generated code should provide a way to parse response references into ResponseTypes, or you can create 
your own ResponseTypes class and inject into a Handler then into a HandlerStack

```php
<?php 
namespace App;

use OpenAPI\Runtime\ResponseTypes;
use OpenAPI\Runtime\ResponseHandler\JsonResponseHandler;
use OpenAPI\Runtime\ResponseHandlerStack\ResponseHandlerStack;

ResponseTypes::setTypes([
    'operation_id' =>[ // This should be unique to $ref as defined in the OpenAPI doc
        '200.' => 'YourGeneratedModelClass::class', // We add a dot after there HTTP status code to enforce string type
        '404.' => 'ErrorModel::class'
    ]
]);

class MyResponseHandlerStack extends ResponseHandlerStack
{
    public function __construct(?ResponseTypesInterface $responseTypes = null)
    {
        $handler = new JsonResponseHandler();
        if ($responseTypes) {
            $handler->setResponseTypes($responseTypes);
        }

        parent::__construct([$handler]);
    }
}  
```


Then in the generated code you [API class](./src/AbstractAPI.php) should set have the $responseHandlerStack class name 
ready (a instance will be created on API instantiation) 

```php
<?php
// Should be generated code here
namespace App\GeneratedCode;

use OpenAPI\Runtime\AbstractAPI;

class Customer extends AbstractAPI{
    protected static $responseHandlerStack = MyResponseHandlerStack::class;
    
    public function get($id)
    {
        return $this->request('operation_id', 'GET',"/customer/${id}");
    }
    
    public function post(array $payload)
    {
        return $this->request('post_operation_id','POST','/customer/',$payload);
    }
}
```

## Projects using this lib

- [allansun/openapi-code-generator](https://github.com/allansun/openapi-code-generator)
- [bricre/royalmail-tracking-v2-sdk](https://gihub.com/bricre/royalmail-tracking-v2-sdk)
- [kubernetes/php-client](https://packagist.org/packages/kubernetes/php-client)
