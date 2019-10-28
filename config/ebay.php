<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modes
    |--------------------------------------------------------------------------
    |
    | This package supports sandbox and production modes.
    | You may specify which one you're using throughout
    | your application here.
    |
    | Supported: "sandbox", "production"
    |
    */

    'mode' => 'production',


    /*
    |--------------------------------------------------------------------------
    | Site Id
    |--------------------------------------------------------------------------
    |
    | The unique numerical identifier for the eBay site your API requests are to be sent to.
    | For example, you would pass the value 3 to specify the eBay UK site.
    | A complete list of eBay site IDs is available
    |(http://developer.ebay.com/devzone/finding/Concepts/SiteIDToGlobalID.html).
    |
    |
    */

    'siteId' => getenv('EBAY_SITE_ID', '0'),

    /*
    |--------------------------------------------------------------------------
    | KEYS
    |--------------------------------------------------------------------------
    |
    | Get keys from EBAY. Create an app and generate keys.
    | (https://developer.ebay.com)
    | You can create keys for both sandbox and production also
    | User token can generated here.
    |
    */

    'sandbox' => [
        'credentials' => [
            'devId' => getenv('EBAY_SANDBOX_DEV_ID'),
            'appId' => getenv('EBAY_SANDBOX_APP_ID'),
            'certId' => getenv('EBAY_SANDBOX_CERT_ID'),
        ],
        'authToken' => getenv('EBAY_SANDBOX_AUTH_TOKEN'),
        'oauthUserToken' => getenv('EBAY_SANDBOX_OAUTH_USER_TOKEN'),
    ],
    'production' => [
        'credentials' => [
            'devId' => getenv('EBAY_PROD_DEV_ID'),
            'appId' => getenv('EBAY_PROD_APP_ID'),
            'certId' => getenv('EBAY_PROD_CERT_ID'),
        ],
        'authToken' => getenv('EBAY_PROD_AUTH_TOKEN'),
        'oauthUserToken' => '',
    ]
];
