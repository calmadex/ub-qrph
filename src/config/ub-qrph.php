<?php

return [
    'base_url' => env('QRPH_BASE_URL'),
    'grant_type' => env('QRPH_GRANT_TYPE', 'password'),
    'scope' => env('QRPH_SCOPE', 'qrph_instapay'),
    'username' => env('QRPH_USERNAME'),
    'password' => env('QRPH_PASSWORD'),
    'partner_id' => env('QRPH_PARTNER_ID'),
    'client_id' => env('QRPH_CLIENT_ID'),
    'client_secret' => env('QRPH_CLIENT_SECRET'),
    'request_access_token_endpoint' => env('QRPH_REQUEST_ACCESS_TOKEN_ENDPOINT', 'partners/sb/partners/v1/oauth2/token'),
    'generate_instapay_qr_string_endpoint' => env('QRPH_GENERATE_INSTAPAY_QR_ENDPOINT', 'partners/sb/qrph/p2m/v1/instapay/nonmsme/qr/generate'),
    'get_settlement_data_endpoint' => env('QRPH_GET_SETTLEMENT_DATA_ENDPOINT', 'partners/sb/qrph/p2m/v1/reports/nonmsme'),
    'client_id_header_name' => env('QRPH_CLIENT_ID_HEADER_NAME', 'x-ibm-client-id'),
    'client_secret_header_name' => env('QRPH_CLIENT_SECRET_HEADER_NAME', 'x-ibm-client-secret'),
    'partner_id_header_name' => env('QRPH_PARTNER_ID_HEADER_NAME', 'partnerId'),
    'guzzle' => []
];