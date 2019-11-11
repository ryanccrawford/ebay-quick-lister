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

        $credentials = [
            'appId' => env('EBAY_PROD_APP_ID'),
            'certId' => env('EBAY_PROD_CERT_ID'),
            'devId' => env('EBAY_PROD_DEV_ID'),
        ];

        $this->OAuthService = new \DTS\eBaySDK\OAuth\Services\OAuthService(
            [
                'credentials' => $credentials,
                'ruName' => env('EBAY_PROD_RUNAME'),
            ]
        );

        if(!$request->hasSession()){

            $this->getauth($request);
        }
        if ($this->isTokenExpired()) {
            dump($request->path());
            $this->doOAuth($request->path());
            $this->getauth($request);
        }
    }

    public function getauth(Request $request)
    {
        $rt = '';
        if ($request->session()->has('return')) {


            $rt = $request->path->sendBack();
        } else {

            $rt = url()->previous();
        }

        $this->doOAuth($rt);


        if ($request->session()->has('user_token')) {

            if (!$this->isTokenExpired()) {
                session()->forget('return');
                return redirect($rt);
            }

            if ($this->isTokenExpired() && $this->session()->has('refresh_token')) {

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
                    $this->setSessionToken(null, null, $response);
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
                'state' => $rt,
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
        $scope = 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account';

        session(['scope' => $scope]);
        if ($returnURL === '') {
            $returnURL = 'dashboard';
        }
        session(['return' => $returnURL]);
    }


    public function setSessionToken(\DTS\eBaySDK\OAuth\Types\GetAppTokenRestResponse $appResponse = null, \DTS\eBaySDK\OAuth\Types\GetUserTokenRestResponse $userResponse = null, \DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestResponse $refreshResponse = null)
    {

        $response = $appResponse !== null ? $appResponse : $userResponse !== null ? $userResponse : $refreshResponse;

        if ($response === null) {

            abort(401);
        }

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

            $return = url()->previous();
        } elseif (session('return') !== null && session('return')  !== '') {

            $return = session('return');
            session()->forget('return');
        }

        return $return;
    }

    public function oauth(Request $request)
    {
        $code = $request->query('code');

        if (strlen($code)) {

            $response = $this->OAuthService->getUserToken(
                new \DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest(
                    [
                        'code' => $code,
                    ]
                )
            );
            $rt = $request->query('state');
            dup($rt);

            if ($response->getStatusCode() !== 200) {

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

                $this->setSessionToken(null, $response, null);

                if (strlen($rt)) {

                    $rd = $this->getReturnUrl('');
                    return redirect($rt);
                }

                if ($request->session()->has('return')) {
                    $rd = $this->getReturnUrl('');
                    return redirect($rd);
                }
            }
        }

        $rd = $this->getReturnUrl('');
        return redirect($rd);
    }
}
