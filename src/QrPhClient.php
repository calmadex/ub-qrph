<?php

namespace Ezi\UbQrPh;

use Ezi\UbQrPh\Contracts\QrPhInterface;
use Ezi\UbQrPh\Exceptions\RequestFailedException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

class QrPhClient implements QrPhInterface
{
    public $httpClient;
    protected array $config;
    /**
     * @var array|mixed
     */
    protected mixed $guzzle;
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected mixed $baseUri;

    /**
     * Construct
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->baseUri = config('ub-qrph.base_url');
        $this->guzzle = $config['guzzle'] ?? [];
    }

    /**
     * Get an instance of the Guzzle HTTP client.
     *
     * @return Client
     */
    protected function httpClient(): Client
    {
        $this->guzzle['base_uri'] = $this->baseUri;

        if (is_null($this->httpClient)) {
            $this->httpClient = new Client($this->guzzle);
        }

        return $this->httpClient;
    }

    /**
     * Authenticate
     *
     * @return mixed|void
     * @throws RequestFailedException
     */
    public function requestAccessToken()
    {
        try {

            $response = $this->httpClient()->post(
                uri: $this->config['request_access_token_endpoint'],
                options: [
                    'form_params' => [
                        'grant_type' => $this->config['grant_type'],
                        'scope' => $this->config['scope'],
                        'username' => $this->config['username'],
                        'password' => $this->config['password'],
                        'client_id' => $this->config['client_id'],
                    ],
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException | ClientException | ConnectException | ServerException $exception) {
            $this->handleException($exception, 'Request Access Token');
        }
    }

    /**
     * Generate Instapay QR String
     *
     * @param string $accessToken
     * @param string $billerCode
     * @param string $storeName
     * @param string $cashierCode
     * @param string $mobileNumber
     * @param string|null $amount
     * @return mixed|void
     * @throws RequestFailedException
     */
    public function generateInstapayQrString(
        string $accessToken,
        string $billerCode,
        string $storeName,
        string $cashierCode,
        string $mobileNumber,
        ?string $amount = ''
    ) {
        try {
            $response = $this->httpClient()->post(
                uri: $this->config['generate_instapay_qr_string_endpoint'],
                options: [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        $this->config['client_id_header_name'] => $this->config['client_id'],
                        $this->config['client_secret_header_name'] => $this->config['client_secret'],
                        $this->config['partner_id_header_name'] => $this->config['partner_id'],
                    ],
                    'json' => [
                        'billerCode' => $billerCode,
                        'storeName' => $storeName,
                        'cashierCode' => $cashierCode,
                        'mobileNumber' => $mobileNumber,
                        'amount' => $amount ?? '',
                    ],
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException | ClientException | ConnectException | ServerException $exception) {
            $this->handleException($exception, 'Generate Instapay QR');
        }
    }

    /**
     * Get Settlement Data
     *
     * @param string $accessToken
     * @param int $page
     * @param int $billerId
     * @param bool $asc
     * @param string|null $storeId
     * @param string|null $cashierCode
     * @param string|null $senderReferenceNo
     * @return mixed|void
     * @throws RequestFailedException
     */
    public function getSettlementData(
        string $accessToken,
        int $page,
        int $billerId,
        bool $asc = true,
        ?string $storeId = '',
        ?string $cashierCode = '',
        ?string $senderReferenceNo = ''
    ) {
        try {
            $response = $this->httpClient()->get(
                uri: $this->config['get_settlement_data_endpoint'],
                options: [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        $this->config['client_id_header_name'] => $this->config['client_id'],
                        $this->config['client_secret_header_name'] => $this->config['client_secret'],
                        $this->config['partner_id_header_name'] => $this->config['partner_id'],
                    ],
                    'query' => [
                        'page' => $page,
                        'billerId' => $billerId,
                        'asc' => $asc ? 'true' : 'false',
                        'storeId' => $storeId,
                        'cashierCode' => $cashierCode,
                        'senderReferenceNo' => $senderReferenceNo,
                    ],
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException | ClientException | ConnectException | ServerException $exception) {
            $this->handleException($exception, 'Get Settlement Data');
        }
    }

    /**
     * Handle Exceptions
     *
     * @param \Exception|ClientException|GuzzleException|ConnectException|ServerException $exception
     * @param string $event
     * @return void
     * @throws RequestFailedException
     */
    private function handleException(
        \Exception|ClientException|GuzzleException|ConnectException|ServerException $exception,
        string $event
    ): void
    {
        throw new RequestFailedException(
            errorCode: $exception->getCode(),
            errorBody: json_decode($exception->getResponse()->getBody()->getContents(), true),
            requestName: $event
        );
    }
}