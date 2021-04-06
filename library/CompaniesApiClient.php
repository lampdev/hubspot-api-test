<?php

namespace Lampdev\Hubspot;

/**
 * Companies Objects API Interaction Class
 */
class CompaniesApiClient extends ApiClientAbstract
{
    /**
     * Return API Base URL
     * add companies endpoint path
     *
     * @return string
     */
    protected function getApiBaseUrl() : string
    {
        return (parent::getApiBaseUrl() . 'companies/');
    }

    /**
     * Find Company by Provided Filter
     *
     * @throws HubspotException
     *
     * @param array $filter array with key => value filter options
     * @return bool|int false or Company ID
     */
    public function findCompany(
        array $filter
    ) : bool|int {
        if (empty($filter['merchant_id'])) {
            throw new HubspotException(
                '`merchant_id` is required!'
            );
        }

        $response = $this->requestSearch(
            $this->validateAndFilterProperties($filter),
            ['id'],
            ['id']
        );

        if (empty($response['results'])) {
            return false;
        }

        return array_shift($response['results'])['id'];
    }

    /**
     * Update Company by ID with the provided Props Data
     *
     * @throws HubspotException
     *
     * @param integer $companyId Company ID
     * @param array $properties Properties Array Key => Value
     * @return bool|array returns Company Properties Array or false
     */
    public function updateCompany(
        int $companyId,
        array $properties
    ) : bool|array {
        if ($companyId <= 0) {
            throw new HubspotException('Bad Company ID provided!');
        }

        $response = $this->requestUpdate(
            $companyId,
            $this->validateAndFilterProperties($properties)
        );

        return ($response['properties'] ?? false);
    }

    private function validateAndFilterProperties(array $properties)
    {
        $filteredProperties = [];

        foreach ($properties as $propertyName => $propertyValue) {
            switch ($propertyName) {
                case 'merchant_id':
                    if (!filter_var($propertyValue, \FILTER_VALIDATE_INT)) {
                        throw new HubspotException(
                            '`merchant_id` field did not pass validation!'
                        );
                    }

                    $filteredProperties['merchant_id'] = intval($propertyValue);
                    break;

                case 'company_risk_tag':
                    if (empty($propertyValue)) {
                        throw new HubspotException(
                            '`company_risk_tag` empty provided!'
                        );
                    }

                    $filteredProperties['company_risk_tag'] = (
                        is_array($propertyValue)
                            ? implode(';', $propertyValue)
                            : $propertyValue
                    );
                    break;

                case 'domain':
                    if (empty($propertyValue)) {
                        throw new HubspotException(
                            '`domain` empty provided!'
                        );
                    }

                    $filteredProperties['domain'] = $propertyValue;
                    break;
            }
        }

        return $filteredProperties;
    }
}
