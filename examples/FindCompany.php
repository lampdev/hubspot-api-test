<?php

require_once('vendor/autoload.php');

use Lampdev\Hubspot\CompaniesApiClient;

$hsClient = new CompaniesApiClient([
    'api_key' => '__INSERT_YOUR_API_KEY_HERE__'
]);

$foundHsCompanyId = $hsClient->findCompany([
    'merchant_id' => '__TEST_MERCHANT_ID__'
]);

echo '$foundHsCompanyId = ';
var_dump($foundHsCompanyId);
echo PHP_EOL;
