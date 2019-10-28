<?php

namespace App\Http\Controllers\Ebay\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        
        
    
    }

    public function getauth(Request $request)
    {

        $this->credentials = [
            'appId' => getenv('EBAY_PROD_APP_ID'),
            'certId' => getenv('EBAY_PROD_CERT_ID'),
            'devId' => getenv('EBAY_PROD_DEV_ID'),
        ];

        $this->OAuthService = new \DTS\eBaySDK\OAuth\Services\OAuthService(
            [
                'credentials' => $this->credentials,
                'ruName' => getenv('EBAY_PROD_RUNAME'),
            ]
        );
       
      // $scope = $request->session('scope');
       $scope = 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account';

        $s = explode(' ', $scope);
        $url =  $this->OAuthService->redirectUrlForUser(
            [
                'state' => 'full',
                'scope' => $s
            ]
        );
        session()->forget('scope');
        return redirect()->away($url);
    }


    public function oauth(Request $request)
    {
          
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
              
                return redirect('home');
            } else {
                session(['token' => $response->access_token]);
                if ($request->session()->has('return')) {
           
                    $rd = session('return') ? session('return') : 'home';//  session('return');
                    session()->forget('return');
                    return redirect($rd);
                }

                
            }
       
            return redirect('home');
        }
    }
}
