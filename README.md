# Nubersoft API Service
Basic API Service using the Nubersoft and Smart DTO Libraries

## Example:
```php
<?php
use \Nubersoft\Api\ {
    App
};

use \Nubersoft\Api\Models\ {
    Auth,
    Auth\JWT,
    App as SetUp,
    ErrorHandler
};

use \Nubersoft\Api\Dto\ {
    Auth\Validate
};

use \Nubersoft\ {
    nQuery as Db
};

use \SmartDto\Dto;

try {
    # Set default return keys as camel case
    App::$responseType = 'c';
    # Create an alternate override folder for calling data
    App::addCoreClass('\\Beyond');
    # Create an alternate override folder for calling data
    App::addCoreDtoClass('\\Beyond');
    # Start API
    $Api = new App();
    # Set up the response headers and fetch the bearer
    $SetUp = $Api->setUp(
        new SetUp(),
        # Create a catchall for errors and report inside API
        new ErrorHandler(function($errCode, $errMsg) {
            throw new \Api\Exception($errMsg, $errCode);
        }, E_ALL)
    );
    # Use the internal user authenticator
    $Api->useAuthenticator(
       ...[
            # Internal use authenticator class
            new Auth(...[
                    # Db to fetch internal user
                    new Db(),
                    # Validate object using bearer
                    $validator = new Validate($SetUp->headers)
                ]
            ),
            # Validate a JWT token
            new JWT($validator)
        ]
    );
    # Allows a callable do before action
    //$Api->beforeInit(function(\Nubersoft\nApp $nApp) use ($Api) {
    //});
    # Run the API
    $response = $Api->init();
    # If smart dto, allow different key return formats
    if($response instanceof Dto) {
        switch($Api->getReturnType()) {
            case('c'):
                $data = $response->toCamelCase();
                break;
            case('p'):
                $data = $response->toPascalCase();
                break;
            default:
                $data = $response->toArray();
        }
    }
    else {
        $data = $response;
    }
}
catch (\Api\Exception $e) {
    $e->halt();
}
catch (\Exception $e) {
    $data = [
        'error' => 'An program error occurred',
        'code' => $e->getCode()
    ];
}
# Write out the response
$this->ajaxResponse($data);
