<?php

namespace Ezi\UbQrPh\Contracts;

interface QrPhInterface
{
    /**
     * Request Access Token
     *
     */
    public function requestAccessToken();


    /**
     * Generate Instapay QR String
     *
     * @param string $accessToken
     * @param string $billerCode
     * @param string $storeName
     * @param string $cashierCode
     * @param string $mobileNumber
     * @param string|null $amount
     */
    public function generateInstapayQrString(
        string $accessToken,
        string $billerCode,
        string $storeName,
        string $cashierCode,
        string $mobileNumber,
        ?string $amount
    );

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
     */
    public function getSettlementData(
        string $accessToken,
        int $page,
        int $billerId,
        bool $asc,
        ?string $storeId,
        ?string $cashierCode,
        ?string $senderReferenceNo
    );
}