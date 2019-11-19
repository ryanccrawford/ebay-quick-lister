<?php

namespace App\Http\Controllers\Ebay\OAuth;

use App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use DTS\eBaySDK\OAuth;
use DTS\eBaySDK\OAuth\Services;

use function HighlightUtilities\getAvailableStyleSheets;

class OAuthController extends Controller
{
    public $credentials;
    public $service;
    public $OAuthService;
    protected $scope;
    protected $token;


    /**
     * Gets a user OAuth Token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __construct(Request $request)
    {
        
            $this->scope = explode(' ', 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account');
            $this->marketPlaceId = \DTS\eBaySDK\Account\Enums\MarketplaceIdEnum::C_EBAY_US;
           $this->credentials = [
                'appId' => env('EBAY_PROD_APP_ID'),
                'certId' => env('EBAY_PROD_CERT_ID'),
                'devId' => env('EBAY_PROD_DEV_ID'),
                'authToken' => env('EBAY_PROD_AUTH_TOKEN'),
                'globalId' => env('EBAY_GLOBAL_ID')
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
       
        if ($request->session()->has('user_token')) {

            if (!$this->isTokenExpired()) {
                session()->forget('return');
                return redirect(url()->previous());
            }

            if ($this->isTokenExpired() && $request->session()->has('refresh_token')) {

                session()->forget('user_token');

                $RefreshUserTokenRequest =  new \DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest(
                    [
                        'refresh_token' => session('refresh_token'),
                        'scope' => $this->scope,
                    ]
                );

                $OauthUserTokenResponse = $this->OAuthService->refreshUserToken($RefreshUserTokenRequest);

                if ($OauthUserTokenResponse->getStatusCode() == 200) {
                    $this->setSessionToken($OauthUserTokenResponse);
                    return redirect(url()->previous());
                } else {
                    session()->forget(
                        [
                            'user_token',
                            'expires_in',
                            'refresh_token'
                        ]
                    );
                    abort(401);
                   //return $this->getauth($request);
                }
            }
        }


        $url =  $this->OAuthService->redirectUrlForUser(
            [
                'state' => url()->previous(),
                'scope' => $this->scope,
            ]
        );

        return redirect()->away($url);
    }

    public function doOAuth(string $returnURL)
    {
        session()->forget('totalPages');
        session()->forget('user_token');
        session()->forget('scope');
        session()->forget('return');
      
        session(['scope' => $this->scope]);
        session(['return' => $returnURL]);
      
    }


    public function setSessionToken($response)
    {
       
        $seconds = $response->expires_in;
        $expiresdatetime = date("Y-m-d H:i:s", strtotime(('+' . $seconds . ' seconds'), strtotime(date("Y-m-d H:i:s"))));
        session(
            [
                'user_token' => $response->access_token,
                'expires_in' => $expiresdatetime,
                'refresh_token' => $response->refresh_token
            ]
        );
    }

    public function isTokenExpired()
    {

        if (session('expires_in') === null || session('expires_in') === '') {
            return true;
        }
        $ed = new DateTime(session('expires_in'));
        $nd = new DateTime(now());

        if ($ed > $nd) {
            return false;
        }

        return true;
    }

    public function getReturnUrl(string $returnURL = 'prev')
    {
        $return = '';
        if ($returnURL === 'prev') {

            $return = url()->current();
        } elseif (session('return') !== null && session('return')  !== '') {

            $return = session('return');
            session()->forget('return');
        }
        dump($return);
        return $return;
    }

    public function oauth(Request $request)
    {
        $code = $request->query('code');
       
        if (strlen($code)) {

            $userTokenRequest = new \DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest(
                                    [
                                        'code' => $code,
                                    ]
                );

            $userTokenresponse = $this->OAuthService->getUserToken($userTokenRequest);

            $rt = $request->query('state');
            
            if ($userTokenresponse->getStatusCode() !== 200) {

                session()->forget(
                    [
                        'user_token',
                        'expires_in',
                        'refresh_token',
                    ]
                );
                //TODO: Create Error page that shows OAuth Errors
                abort(401);
            } else {
                
                $this->setSessionToken($userTokenresponse);
                $rd = $rt;
                return redirect($rd);
                
            }
        }

        $rd = $this->getReturnUrl('');
        return redirect($rd);
    }

    public function getService($serviceName, $serviceRequest)
    {
       // 'verifyAddFixedPriceItem', ($serviceRequest)
       
       if ($this->service === null) {
           $this->service = new \DTS\eBaySDK\Trading\Services\TradingService(
                [
                    'siteId' => '0',
                    'authorization' => session('user_token'),
                    'credentials' => $this->credentials
                ]
            );
       }

       return $this->service->$serviceName($serviceRequest);

    }

}
