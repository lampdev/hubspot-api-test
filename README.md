# hubspot-api-test
HubSpot API Integration Package [TEST]

This package can be delivered to the project as a composer package:
```
composer require lampdev/hubspot-api-test
```

Please make sure you installed composer autoloader into your project:
```
require_once('vendor/autoload.php');
```

Initialize the HubSpot Company API Client with your API Key:
```
$hsClient = new \Lampdev\Hubspot\CompaniesApiClient([
    'api_key' => '__INSERT_YOUR_API_KEY_HERE__'
]);
```

There are 2 methods available at the moment:
 - [CompaniesApiClient::findCompany()](examples/FindCompany.php)
 - [CompaniesApiClient::updateCompany()](examples/UpdateCompany.php)
