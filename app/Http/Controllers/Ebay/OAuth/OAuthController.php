<?php

namespace App\Http\Controllers\Ebay\OAuth;

use App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use DTS\eBaySDK\OAuth;
use DTS\eBaySDK\OAuth\Services;


class OAuthController extends Controller
{
    protected $credentials;
    protected $OAuthService;
    protected $code;
    protected $scope;
    /**
     * Gets a user OAuth Token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->scope = explode(' ', 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account');

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


        if ($request->session()->has('user_token')) {

            if (!$this->isTokenExpired()) {
                dump('User token has expired');
                $rt = $this->sendBack();
                return redirect($rt);
            }

            die;
            if ($this->isTokenExpired() && $this->session()->has('refresh_token') && $this->session()->has('return')) {

                session()->forget('user_token');

                $response = $this->OAuthService->refreshUserToken(
                    new \DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest(
                        [
                            'refresh_token' => session('refresh_token'),
                            'scope' => $this->scope,
                        ]
                    )
                );

                if ($response->getStatusCode() == 200) {
                    $this->setSessionToken($response);
                    $rt = $this->sendBack();
                    return redirect($rt);
                } else {
                    session()->forget(
                        [
                            'user_token',
                            'expires_in',
                            'refresh_token'
                        ]
                    );

                    return redirect('getauth');
                }
            }
        }


        $url =  $this->OAuthService->redirectUrlForUser(
            [
                'state' => 'full',
                'scope' => $this->scope,
            ]
        );
        dump("Getting ready to redirect<br>");

        return redirect()->away($url);
    }

    public function setSessionToken($response)
    {
        $seconds = $response->expires_in;
        dump('Seconds till expired: ' . $seconds);
        $expiresdatetime = date("Y-m-d H:i:s", strtotime(('+' . $seconds . ' seconds'), strtotime(date("Y-m-d H:i:s"))));
        dump($expiresdatetime);
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
        dump($ed);
        dump($nd);
        dump('session expires: ' . $ed > $nd);
        if ($ed > $nd) {
            dump("not expire<br>");

            return false;
        }

        return true;
    }

    public function sendBack()
    {
        $return = session('return');
        session()->forget('return');
        return $return;
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

            dump($response->getStatusCode());

            if ($response->getStatusCode() !== 200) {

                session()->forget(
                    [
                        'user_token',
                        'expires_in',
                        'refresh_token',
                    ]
                );

                return redirect('auth');
            } else {

                $this->setSessionToken($response);

                if ($request->session()->has('return')) {

                    $rd = session('return') ? session('return') : 'home'; //  session('return');
                    session()->forget('return');
                    return redirect($rd);
                }
            }
        }

        return redirect('home');
    }
}
