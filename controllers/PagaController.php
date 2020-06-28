<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/paga-utility.php';

class PagaController
{
    private $namespace;
    private $secret;
    private $principal;
    private $hash;
    private $baseUrl;
    private $token;
    private $isSetup;

    public function __construct()
    {
        $this->namespace     = 'paga/v1';
        $this->secret = get_option('paga_secret');
        $this->principal = get_option('paga_credential');
        $this->hash = get_option('paga_hmac');
        $this->baseUrl = get_option('paga_url');
        $this->token = API_TOKEN;

        $this->isSetup = paga_is_setup();
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, 'airtimePurchase', array(
            [
                'methods'   => 'POST',
                'callback'  => [$this, 'airtimePurchase'],
                'permission_callback' => [ $this, 'permission'],
            ]
        ));

        register_rest_route($this->namespace, 'getMerchants', array(
            [
                'methods'   => ['POST', 'GET'],
                'callback'  => [$this, 'getMerchants'],
                'permission_callback' => [ $this, 'permission' ],
            ]
        ));

        register_rest_route($this->namespace, 'getMerchantServices', array(
            [
                'methods'   => 'POST',
                'callback'  => [$this, 'getMerchantServices'],
                'permission_callback' => [ $this, 'permission' ],
            ]
        ));
    }

    public function permission(){
        $queries = [];
        parse_str($_SERVER['QUERY_STRING'], $queries);
        if(!array_key_exists('api_key', $queries)){
            return false;
        }
        $apiKey = $queries['api_key'];
        return $apiKey == $this->token;
    }

    public function airtimePurchase(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        $amount = $data['amount'];
        $destination = $data['destination_phone_number'];
        $reference = paga_generate_reference();
        $sha512 = paga_get_SHA512("{$reference}{$amount}{$destination}{$this->hash}");
        $post = [
            "referenceNumber" => $reference,
            "amount" => $amount,
            "destinationPhoneNumber" => $destination
        ];
        $url = $this->baseUrl.'/paga-webservices/business-rest/secured/airtimePurchase';
        $response = $this->curl($url, $post, $sha512);
        do_action('paga_transaction_log', $reference, 'PAGA', 'AIRTIME PURCHASE', $response);
        return rest_ensure_response(json_decode($response));
    }

    private function curl($url, $postData, $sha512){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
                "credentials: $this->secret",
                "hash: $sha512",
                "principal: $this->principal",
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getMerchants(\WP_REST_Request $request){
        if(!$this->isSetup){
            return rest_ensure_response($this->emptyResponse('merchants'));
        }
        $filter = boolval($request->get_param( 'filter' )); // should filter merchants based on configured merchants on settings?
        $url = $this->baseUrl. '/paga-webservices/business-rest/secured/getMerchants';
        $reference = paga_generate_reference();
        $sha512 = paga_get_SHA512("{$reference}{$this->hash}");
        $post = [
            "referenceNumber" => $reference,
        ];
        $response = $this->curl($url, $post, $sha512);
        $data = json_decode($response);
        if(!$filter){
            return rest_ensure_response($data);
        }
        $options = get_option('paga_merchants');
        if($data->responseCode == 0 && $data->message == 'Success'){
            $merchants = $data->merchants;
            $selectedMerchants = [];
            foreach($merchants as $merchant){
                if(in_array($merchant->uuid, $options)){
                    $selectedMerchants[] = $merchant;
                }
            }
            $responseData = [
                'responseCode' => $data->responseCode,
                'message' => 'Success',
                'merchants' => $selectedMerchants
            ];
        }else{
            $responseData = [
                'responseCode' => $data->responseCode,
                'message' => 'Failure',
                'errorMessage' => $data->errorMessage
            ];
        }
        return rest_ensure_response($responseData);
    }

    public function getMerchantServices(\WP_REST_Request $request){
        if(!$this->isSetup){
            return rest_ensure_response($this->emptyResponse('services'));
        }
        $url = $this->baseUrl.'/paga-webservices/business-rest/secured/getMerchantServices';
        $data = $request->get_params();
        $merchant = $data['merchant'];
        $reference = paga_generate_reference();
        $post = [
            'merchantPublicId' => $merchant,
            'referenceNumber' => $reference
        ];
        $sha512 = paga_get_SHA512("{$reference}{$merchant}{$this->hash}");
        $response = $this->curl($url, $post, $sha512);
        return rest_ensure_response(json_decode($response));
    }

    public function merchantPayment(WP_REST_Request $request){
        $url = $this->baseUrl.'/paga-webservices/business-rest/secured/merchantPayment';
        $data = $request->get_params();
        $reference = paga_generate_reference();
        $amount = $data['amount'];
        $merchantAccount = $data['merchant_account'];
        $merchantReferenceNumber = $data['merchant_reference_number'];
        $merchantService = $data['merchant_service'];
        $post = [
            'referenceNumber' => $reference,
            'amount' => $amount,
            'merchantAccount' => $merchantAccount,
            'merchantReferenceNumber' => $merchantReferenceNumber,
            'merchantService' => $merchantService
        ];
        $sha512 = paga_get_SHA512("{$reference}{$amount}{$merchantAccount}{$merchantReferenceNumber}{$this->hash}");
        $response = $this->curl($url, $post, $sha512);
        return rest_ensure_response(json_decode($response));
    }

    public function emptyResponse($field = 'data'){
        return [
            'responseCode' => 0,
            'status' => 'Success',
            $field => []
        ];
    }
}
