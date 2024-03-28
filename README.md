# PACKAGE UB - QRPH

## Install

- Run `composer require ezi/ub-qrph`
- Publish config files `php artisan vendor:publish --tag=ub-qrph`

## Request Access Token
### Sample Usage

```
$service = new QrPhClient(config('ub-qrph'));
$service->requestAccessToken();
```

### Sample Response
```
array:8 [
  "token_type" => "Bearer"
  "access_token" => "AAIkMzJkMjViYzEtZjkzZi00NDY4LTg4M2UtMjZjZmMwYzFiY2M1ce4NyfKxKUwSz9NK-bb_CrOg7Wiy-OnCerANGm5qTFZD55WgfCWiS37TjF01y-RnBBefu8q5UTxwCV02mC7gpuTYs7kqgV1Wk47t-FBmhlaob-wZKq_CQANdE135daXcGC_3VDJPW5QUSlkWjJw4jljFv6Lzz5ocBEvuZ1_DX6lu94izR4bOGCRWxFDh4rwK_Kzk1gjykHJd4BZSWPRhuMcxT_v1QPii5ml9qmdnCwn_N6W6TzkvO4Mh_QQSYX4h_VHcDUON4KJaDLNef44SMejaHjavuiOKL96aFr55jERauoHRWL-oHJ3qfI5g_Tz9RtVCo1rvUziiDKinxHWpaA"
  "metadata" => "a:rY5CFD9ja3as2I/pjwJgdnZL//vn16eYjwLA8ShFv1ePX2zJgoPk+XQhhzICV98/oPMWGAH6WWknAAuqQ71Mj6isK4Wvn7t0XckPIUzV2j9gCPFZGHI7AunMUWZ+Z1u7NN/iYaIzwlYTIhABw7C2U+n/um5nNZAq6twX"
  "expires_in" => 3600
  "consented_on" => 1711614918
  "scope" => "qrph_instapay"
  "refresh_token" => "AAIa-ewGV4KZW7vfYDPjFMLoxlabqjpAwkbzxi2LuYTQfzEuIPaTYEnz1MOwVo22Wn0YaI7KoSzpl82_NPhaYKBSdWh4Ud29Z_61DqOh96nSvwLhIwtg10CR_UleiyuAzWlgahqalhWIioixEGiQ13pDcRBMwx-KxGSYAxpJaJJykmAf0oQjFSDTSsipxEgQNzKPzZGEr6k3H5fqqytzml-V9L4g_VKnT4gWSVIDzPNMoVhjmOG5kgS467d1preENQXDKbhVUhYuRln_p-4MmjzROesISK_7mH5y323WYUVh3AN2pi0_UgNwFF1vC3pLApWcuqtMKx1nsPmwsCicST6078A0q3-rz8ENyNuncbQDWg"
  "refresh_token_expires_in" => 2682000
]
```

## Generate Instapay QR String
### Sample Usage

```
$service = new QrPhClient(config('ub-qrph'));
try {
    $qrResponse = $service->generateInstapayQrString(
        accessToken: $requestAccessTokenResponse['access_token'],
        billerCode: '9871',             //required
        storeName: 'Sample Store',      //required
        cashierCode: 'Code-123',        //required
        mobileNumber: 639161111111,     //required|should be 09161111111 or 639161111111
        amount: 0                       //optional
    );
} catch (RequestFailedException $e) {
    dd($e->getRequestName());
    dd($e->getErrorCode());
    dd($e->getErrorBody());
    dd($e->toJson());
}
```

### Sample Response

```
array:4 [
  "code" => "TS"
  "state" => "Successfully processed request"
  "uuid" => "29d16b86-a482-469d-8cc8-46424a5131b9"
  "response" => "[qrIdentifier:7103,qrString:00020101021128720011ph.ppmi.p2m0111UBPHPHMMXXX0308999179100419773853011023024339005030105204601653036085802PH5916Merchant Acq-123600312362410012ph.ppmi.qrph0304710305062110000803***80310012ph.ppmi.qrph01031230204710388300012ph.ppmi.qrph0110970941393963045967]"
]
```

## Get Settlement Data
### Sample Usage

```
$service = new QrPhClient(config('ub-qrph'));
try {
    $response = $service->getSettlementData(
        accessToken: $requestAccessTokenResponse['access_token'],
        page:1,                 // required
        billerId: 3,            // required
        asc: true,              // boolean|required
        storeId: '',            // optional
        cashierCode: ''         // optional
        senderReferenceNo: ''   // optional
    );
} catch (RequestFailedException $e) {
    dd($e->getErrorBody());
    dd($e->getRequestName());
    dd($e->getErrorCode());
    dd($e->toJson());
}
```

### Sample Response

```
array:4 [
  "code" => "TS"
  "state" => "Successfully processed request"
  "uuid" => "29d16b86-a482-469d-8cc8-46424a5131b9"
  "response" => "[qrIdentifier:7103,qrString:00020101021128720011ph.ppmi.p2m0111UBPHPHMMXXX0308999179100419773853011023024339005030105204601653036085802PH5916Merchant Acq-123600312362410012ph.ppmi.qrph0304710305062110000803***80310012ph.ppmi.qrph01031230204710388300012ph.ppmi.qrph0110970941393963045967]"
]
```
