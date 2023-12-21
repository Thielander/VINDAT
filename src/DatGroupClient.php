<?php

namespace Thielander\Vindat;

use SoapClient;
use SoapFault;
use SoapHeader;

class DatGroupClient
{
    private $token;
    private $response_vehicle;

    public function __construct()
    {
        $this->authenticate();
    }

    public function authenticate()
    {
        $wsdl_authentication = 'https://www.datgroup.com/FinanceLine/soap/Authentication?wsdl';
        $options_authentication = [
            'trace' => 1,
            'exceptions' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
        ];

        $client_authentication = new SoapClient($wsdl_authentication, $options_authentication);

        $params_authentication = [
            'request' => [
                'customerNumber' => 'YOURCUSTOMERNUMBER',
                'customerLogin' => 'YOURCUSTOMERLOGIN',
                'customerPassword' => 'YOURCUSTOMERPASSWORD',
                'interfacePartnerNumber' => 'INTERFACEPARTNERNUMBER',
                'interfacePartnerSignature' => 'INTERFACEPARTNERSIGNATUR',
            ],
        ];

        try {
            $response_authentication = $client_authentication->generateToken($params_authentication);
            $this->token = $response_authentication->token;
        } catch (SoapFault $fault) {
            return false;
        }

        return true;
    }

    private function createStreamContext()
    {
        $aHTTP = [
            'http' => [
                'header' => "User-Agent: PHP-SOAP/" . PHP_VERSION . "\r\n"
                         . "DAT-AuthorizationToken: " . $this->token . "\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        return stream_context_create($aHTTP);
    }

    public function getVehicleData($vin)
    {
        if (empty($this->token)) {
            return false; // Or try authentication again
        }

        $wsdl_vehicle = 'https://www.datgroup.com/FinanceLine/soap/VehicleIdentificationService?wsdl';
        $context = $this->createStreamContext();

        $options_vehicle = [
            'trace' => true,
            'exceptions' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'encoding' => 'UTF-8',
            'soap_version' => SOAP_1_1,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            "stream_context" => $context,
        ];

        try {
            $client_vehicle = new SoapClient($wsdl_vehicle, $options_vehicle);

            $namespace = 'http://sphinx.dat.de/services/VehicleIdentificationService';
            $headerData = ['token' => $this->token];
            $header = new SoapHeader($namespace, 'Authentication', $headerData);

            $client_vehicle->__setSoapHeaders($header);

            $data = [
                'request' => [
                    'sessionID' => 'YOURSESSIONID',
                    'locale' => [
                        'country' => 'de',
                        'datCountryIndicator' => 'de',
                        'language' => 'de',
                    ],
                    'restriction' => 'ALL',
                    'vin' => $vin,
                    'coverage' => 'ALL',
                ],
            ];

            $this->response_vehicle = $client_vehicle->getVehicleIdentificationByVin($data);
        } catch (SoapFault $fault) {
            return false;
        }

        return true;
    }

    public function getResponse()
    {
        return $this->response_vehicle;
    }
}
