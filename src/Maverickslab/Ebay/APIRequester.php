<?php
    /**
     * Created by PhpStorm.
     * User: Optimistic
     * Date: 17/03/2015
     * Time: 12:22
     */

    namespace Maverickslab\Ebay;


    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Stream\Stream;
    use Illuminate\Support\Facades\Config;

    class APIRequester
    {
        private $http_client;

        public function __construct(Client $http_client)
        {
            $this->http_client = $http_client;
        }

        public function request($inputs, $request_type, $site_id = 0)
        {
            $headers = [
                'Content-Type'                   => 'text/xml',
                'X-EBAY-API-COMPATIBILITY-LEVEL' => config('ebay.api_compatibility_level'),
                'X-EBAY-API-APP-NAME'            => config('ebay.api_app_name'),
                'X-EBAY-API-DEV-NAME'            => config('ebay.api_dev_name'),
                'X-EBAY-API-CERT-NAME'           => config('ebay.api_cert_name'),
                'X-EBAY-API-SITEID'              => $site_id,
                'X-EBAY-API-CALL-NAME'           => $request_type
            ];

            $root_node = "{$request_type}Request";

            $request_body = [];
            $request_body['@attributes'] = [
                "xmlns" => "urn:ebay:apis:eBLBaseComponents"
            ];

            $request_body['WarningLevel'] = config('ebay.warning_level');
            $request_body['ErrorLanguage'] = config('ebay.error_language');

            $xml = ArrayToXML::createXML($root_node, array_merge($request_body, $inputs));
            $request_body = $xml->saveXML();

            $response = $this->http_client->post(config('ebay.base_url'), [
                'headers' => $headers,
                'body'    => $request_body,
                'verify'  => false
            ]);

            return json_decode(json_encode($response->xml()), true);
        }
    }