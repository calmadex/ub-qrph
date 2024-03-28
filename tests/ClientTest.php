<?php

use Ezi\UbQrPh\Contracts\QrPhInterface;
use Ezi\UbQrPh\Exceptions\RequestFailedException;
use Ezi\UbQrPh\Facade\QrPhFacade;
use Ezi\UbQrPh\QrPhClient;
use Ezi\UbQrPh\ServiceProvider;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class ClientTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = require __DIR__ . '/../src/config/ub-qrph.php';
        $app['config']->set('ub-qrph', $config);
    }

    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * Get CGI instance
     *
     * @return QrPhClient
     */
    protected function service(): QrPhClient
    {
        return $this->app->make(QrPhInterface::class);
    }

    /**
     * Mock response
     *
     * @param array|string $response
     * @param int $status
     * @param array $headers
     *
     * @return void
     */
    protected function mockResponse(
        array $response,
        int $status = 200,
        array $headers = [],
        ?string $event = 'Request Failed',
        bool $isException = false
    ): void {
        $mock = new MockHandler([
            new Response($status, $headers, json_encode($response)),
        ]);
        if ($isException) {
            new MockHandler([
                new RequestFailedException($status, json_encode($response), $event),
            ]);
        }
        $handlerStack = HandlerStack::create($mock);

        $this->app['config']->set('ub-qrph.guzzle', ['handler' => $handlerStack]);
    }

    /**
     * @test
     * @testdox Dependency
     *
     * @return void
     */
    public function dependency(): void
    {
        $this->assertInstanceOf(QrPhClient::class, $this->service());
    }

    /**
     * @test
     * @testdox It can send pay
     *
     * @return void
     */
    public function facade_request_access_token_test(): void
    {
        QrPhFacade::spy();
        $this->mockResponse(json_decode(
            file_get_contents(__DIR__ . '/Responses/request_access_token_success.json'),
            true
        ));

        $response = QrPhFacade::requestAccessToken();
        $this->assertNull($response);
    }

    /**
     * @test
     * @testdox It can send pay
     *
     * @return void
     */
    public function facade_generate_instapay_qr_test(): void
    {
        QrPhFacade::spy();
        $this->mockResponse(json_decode(
            file_get_contents(__DIR__ . '/Responses/request_access_token_success.json'),
            true
        ));

        $response = QrPhFacade::generateInstaypayQrString(
            accessToken: $this->requestAccessToken(),
            billerCode: 9871,
            storeName: 'Sample3',
            cashierCode: 'test',
            mobileNumber: '639178379973',
            amount: 0);

        $this->assertNull($response);
    }

    /**
     * @test
     * @testdox It can send pay
     *
     * @return void
     */
    public function facade_get_settlement_data_test(): void
    {
        QrPhFacade::spy();
        $this->mockResponse(json_decode(
            file_get_contents(__DIR__ . '/Responses/request_access_token_success.json'),
            true
        ));

        $response = QrPhFacade::getSettlementData(
            accessToken: $this->requestAccessToken(),
            page:1,
            billerId: 3,
            asc:3,
            storeId: "Sample Store ID",
            cashierCode: "Sample Cashier Code",
            senderReferenceNo: "Sample Sender Reference No"
        );

        $this->assertNull($response);
    }

    /**
     * @test
     * @testdox It can request access token
     *
     * @return void
     */
    public function request_access_token_success(): void
    {
        $this->mockResponse(json_decode(
            file_get_contents(__DIR__ . '/Responses/request_access_token_success.json'),
            true
        ));

        $response = $this->service()->requestAccessToken();
        $this->assertEquals([
                "token_type" => "Bearer",
                "access_token" => "token",
                "metadata" => "metadata",
                "expires_in" => 0,
                "consented_on" => 0,
                "scope" => "qrph_instapay",
                "refresh_token" => "refresh-token",
                "refresh_token_expires_in" => 0
            ],
            $response
        );
    }

    /**
     * @test
     * @testdox It can generate instapay qr string
     *
     * @return void
     */
    public function generate_instapay_qr_string_success(): void
    {
        $this->mockResponse(json_decode(
            file_get_contents(__DIR__ . '/Responses/generate_instapay_qr_success.json'),
            true
        ));

        $response = $this->service()->generateInstapayQrString(
            $this->requestAccessToken(),
            billerCode: 9871,
            storeName: 'Sample3',
            cashierCode: 'test',
            mobileNumber: '639178379973',
            amount: 0
        );

        $this->assertArrayHasKey('state', $response);
        $this->assertEquals('Successfully processed request', $response['state']);
    }

    /**
     * @test
     * @testdox It can get settlement data
     *
     * @return void
     */
    public function get_settlement_data_success(): void
    {
        $this->mockResponse(json_decode(
            file_get_contents(__DIR__ . '/Responses/get_settlement_data_success.json'),
            true
        ));

        $response = $this->service()->getSettlementData(
            accessToken: $this->requestAccessToken(),
            page:1,
            billerId: 3,
            asc:3,
            storeId: "Sample Store ID",
            cashierCode: "Sample Cashier Code",
            senderReferenceNo: "Sample Sender Reference No"
        );

        $this->assertArrayHasKey('state', $response);
        $this->assertEquals('Successfully processed request', $response['state']);
    }

    /**
     * @test
     * @testdox It can request access token
     *
     * @return void
     */
    public function request_access_token_failed(): void
    {
        $expectedBody = json_decode(
            json: file_get_contents(__DIR__ . '/Responses/request_access_token_failed.json'),
            associative: true
        );

        $this->mockResponse(
            response: $expectedBody,
            status: HttpResponse::HTTP_BAD_REQUEST,
            headers: ['sample-header' => 'header'],
            event: 'Request Access Token',
            isException: true
        );

        try {
            $this->service()->requestAccessToken();
        } catch (RequestFailedException $e) {
            $this->assertEquals(expected: 400, actual: $e->getErrorCode());
            $this->assertEquals(expected: $expectedBody, actual: $e->getErrorBody());
            $this->assertEquals(expected: 'Request Access Token', actual: $e->getRequestName());
        }
    }

    /**
     * @test
     * @testdox It can generate instapay qr string
     *
     * @return void
     */
    public function generate_instapay_qr_string_failed(): void
    {
        $expectedBody = json_decode(
            json: file_get_contents(__DIR__ . '/Responses/generate_instapay_qr_failed.json'),
            associative: true
        );

        $this->mockResponse(
            response: $expectedBody,
            status: HttpResponse::HTTP_BAD_REQUEST,
            headers: ['sample-header' => 'header'],
            event: 'Generate Instapay QR',
            isException: true
        );

        try {
            $this->service()->generateInstapayQrString(
                $this->requestAccessToken(),
                billerCode: 9871,
                storeName: 'Sample3',
                cashierCode: 'test',
                mobileNumber: '+639178379973',
                amount: 0
            );
        } catch (RequestFailedException $e) {
            $this->assertEquals(expected: 400, actual: $e->getErrorCode());
            $this->assertEquals(expected: $expectedBody, actual: $e->getErrorBody());
            $this->assertEquals(expected: 'Generate Instapay QR', actual: $e->getRequestName());
        }
    }

    /**
     * @test
     * @testdox It can get settlement data
     *
     * @return void
     */
    public function get_settlement_data_failed(): void
    {
        $expectedBody = json_decode(
            json: file_get_contents(__DIR__ . '/Responses/get_settlement_data_failed.json'),
            associative: true
        );

        $expectedJson = file_get_contents(__DIR__ . '/Responses/sample_to_json.json');

        $this->mockResponse(
            response: $expectedBody,
            status: HttpResponse::HTTP_BAD_REQUEST,
            headers: ['sample-header' => 'header'],
            event: 'Get Settlement Data',
            isException: true
        );

        try {
             $this->service()->getSettlementData(
                accessToken: $this->requestAccessToken(),
                page: 1,
                billerId: 3,
                asc: 3,
                storeId: "Sample Store ID",
                cashierCode: "Sample Cashier Code",
                senderReferenceNo: "Sample Sender Reference No"
            );
        } catch (RequestFailedException $e) {
            $this->assertEquals(expected: $expectedJson, actual: $e->toJson());
            $this->assertEquals(expected: 400, actual: $e->getErrorCode());
            $this->assertEquals(expected: $expectedBody, actual: $e->getErrorBody());
            $this->assertEquals(expected: 'Get Settlement Data', actual: $e->getRequestName());
        }
    }

    /**
     * Request Access Token
     *
     * @return mixed
     * @throws GuzzleException
     */
    private function requestAccessToken(): mixed
    {
        $this->mockResponse(json_decode(
            file_get_contents(__DIR__ . '/Responses/request_access_token_success.json'),
            true
        ));

        $tokenRequestResponse = $this->service()->requestAccessToken();
        $this->assertArrayHasKey('access_token', $tokenRequestResponse);

        return $tokenRequestResponse['access_token'];
    }
}
