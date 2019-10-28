<?php

namespace App\Http\Controllers\Ebay\OAuth;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use DTS\eBaySDK\OAuth;
use DTS\eBaySDK\OAuth\Services;


class OAuthController extends Controller
{
    protected $credentials;
    protected $OAuthService;
    protected $code;
    /**
     * Gets a user OAuth Token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function __construct()
    {
        $this->middleware('auth');

        $this->credentials = [
            'appId' => env('EBAY_PROD_APP_ID'),
            'certId' => env('EBAY_PROD_CERT_ID'),
            'devId' => env('EBAY_PROD_DEV_ID'),
        ];

        $this->OAuthService = new \DTS\eBaySDK\OAuth\Services\OAuthService(
            [
                'credentials' => $this->credentials,
                'ruName' => env('EBAY_PROD_RUNAME'),
            ]
        );
    }

    public function getauth(Request $request)
    {
        $scope = session('scope');
        echo var_dump($this->credentials);
        echo var_dump($this->OAuthService);
        $s = explode(' ', $scope);
        $url =  $this->OAuthService->redirectUrlForUser(
            [
                'state' => 'bar',
                'scope' => $s
            ]
        );
        return redirect()->away($url);
    }


    public function oauth(Request $request)
    {
        $this->code = $request->query('code');
        if (strlen($this->code)) {
            $response = $this->OAuthService->getUserToken(
                new \DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest(
                    [
                        'code' => $this->code,
                    ]
                )
            );

            if ($response->getStatusCode() !== 200) {
                printf(
                    "%s: %s\n\n",
                    $response->error,
                    $response->error_description
                );
            } else {
                session(['token' => $response->access_token]);
                if ($request->session()->has('return')) {
                    echo var_dump($request->session());
                    $rd =   session('return');
                    return redirect($rd);
                }

                return redirect('home');
            }
        }
    }
}
