<?php

namespace Lampdev\Hubspot;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Abstract Class with defined common
 * request types to CRM Objects API endpoints.
 */
abstract class ApiClientAbstract
{
    private const API_URL               = 'https://api.hubapi.com/crm/v3/objects/';
    private const API_KEY_FIELD_NAME    = 'hapikey';
    private const API_PARSE_JSON_ASSOC  = true;
    private const API_ENDPOINT_SEARCH   = 'search';
    private const API_HTTP_METHOD_POST  = 'POST';
    private const API_HTTP_METHOD_PATCH = 'PATCH';
    private const OPERATOR_NAME_EQUALS  = 'EQ';

    private array $config;

    /**
     * Create API Client Class
     *
     * @param array $config API Credentials and possible other configuration
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * Store config into class properties.
     *
     * @param array $config
     *
     * @throws HubspotException
     *
     * @return void
     */
    private function setConfig(array $config)
    {
        if (empty($config['api_key'])) {
            throw new HubspotException(
                'API Key is required to create API Client!'
            );
        }

        $this->config = $config;
    }

    /**
     * Return API Base URL
     * can be overridden or extended in child classes
     *
     * @return string
     */
    protected function getApiBaseUrl() : string
    {
        return self::API_URL;
    }

    /**
     * Return API Credentials Query Param Array
     *
     * @return array
     */
    private function getApiCredentialsParamArray() : array
    {
        return [
            self::API_KEY_FIELD_NAME => $this->config['api_key']
        ];
    }

    /**
     * Makes API Request and Returns JSON decoded Response Array or throws Exc.
     *
     * @throws HubspotException
     *
     * @param string $method Request HTTP Method
     * @param string $action Request Endpoint
     * @param array $query Request Query String in Assoc. Array Format
     * @param array $postData Request Post Data Assoc. Array
     * @return array Response Data (decoded JSON)
     */
    private function doApiCall(
        string $method,
        string $action,
        array $query = [],
        array $postData = null
    ) : array {
        $httpClient = new GuzzleClient([
            'base_uri' => $this->getApiBaseUrl()
        ]);

        // at least API Credentials are provided via query string params:
        $requestParams = [
            'query' => array_merge(
                $query,
                $this->getApiCredentialsParamArray()
            )
        ];

        // attach post body as JSON post if provided:
        if (!empty($postData)) {
            $requestParams['json'] = $postData;
        }

        try {
            // make API request:
            $httpResponse = $httpClient->request(
                $method,
                $action,
                $requestParams
            );
        } catch (GuzzleException $exception) {
            if ($exception->hasResponse()) {
                $errDetail = json_decode(
                    $exception->getResponse()->getBody()->getContents(),
                    self::API_PARSE_JSON_ASSOC
                );

                if (!empty($errDetail['status'])) {
                    throw new HubspotException(
                        (
                            'API Returned Error [' .
                            $errDetail['status'] .
                            ']: ' .
                            $errDetail['message'] .
                            ' (correlationId:' .
                            $errDetail['correlationId'] .
                            ').'
                        ),
                        0,
                        $exception
                    );
                }
            }

            throw new HubspotException(
                'API Request Failed. Details: ' . $exception->getMessage(),
                0,
                $exception
            );
        }

        return json_decode(
            $httpResponse->getBody()->getContents(),
            self::API_PARSE_JSON_ASSOC
        );
    }

    /**
     * Request Object Search
     *
     * @param array $conditions Key -> Value Search Filter (only exact match implemented)
     * @param array $sorts Sorts By Fields List
     * @param array $returnProps Return Property Names List
     * @param string $query Search Query String
     * @param integer $limit Search Result Limit
     * @param integer $skip Skip First N Records (Pagination Feature)
     * @return void
     */
    protected function requestSearch(
        array $conditions,
        array $sorts,
        array $returnProps,
        string $query = null,
        int $limit = 1,
        int $skip = 0
    ) {
        if (empty($conditions)) {
            throw new HubspotException('Search Conditions are required!');
        }

        if (empty($sorts)) {
            throw new HubspotException('Search Sorts are required!');
        }

        if (empty($returnProps)) {
            throw new HubspotException(
                'Search Return Properties List is required!'
            );
        }

        $requestData = [
            'filterGroups' => [],
            'sorts'      => $sorts,
            'properties' => $returnProps,
            'limit'      => $limit,
            'after'      => $skip
        ];

        // according to the task description there should be
        // implemented only exact match - EQ operator, so it is hardcoded
        // to make possible the method to accept [key => value]
        // search filter array, anyway, this can be easily changed here
        foreach ($conditions as $conditionField => $conditionValue) {
            $requestData['filterGroups'][] = [
                'filters' => [[
                    'value'        => $conditionValue,
                    'propertyName' => $conditionField,
                    'operator'     => self::OPERATOR_NAME_EQUALS
                ]]
            ];
        }

        if (!empty($query)) {
            $requestData['query'] = $query;
        }

        return $this->doApiCall(
            self::API_HTTP_METHOD_POST,
            self::API_ENDPOINT_SEARCH,
            [],
            $requestData
        );
    }

    /**
     * Request Object Update
     *
     * @param integer $objectId Updating Object ID
     * @param array $propertiesData Data to Update
     * @return void
     */
    protected function requestUpdate(
        int $objectId,
        array $propertiesData
    ) {
        return $this->doApiCall(
            self::API_HTTP_METHOD_PATCH,
            $objectId,
            [],
            [
                'properties' => $propertiesData
            ]
        );
    }
}
