<?php

require_once('vendor/autoload.php');

use Lampdev\Hubspot\CompaniesApiClient;

$hsClient = new CompaniesApiClient([
    'api_key' => '__INSERT_YOUR_API_KEY_HERE__'
]);

$updatedCompanyProps = $hsClient->updateCompany(
    '__TEST_HS_COMPANY_ID__',
    [
        'merchant_id'      => '__UPDATED_MERCHANT_ID__',
        'company_risk_tag' => '__UPDATED_RISK_TAGS_STR_OR_ARRAY__',
        'domain'           => '__UPDATED_DOMAIN_STR__'
    ]
);

echo '$updatedCompanyProps = ';
var_dump($updatedCompanyProps);
echo PHP_EOL;
