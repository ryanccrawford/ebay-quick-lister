<?php

namespace App\Http\Controllers\Ebay\OAuth;

use App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use DTS\eBaySDK\OAuth;
use DTS\eBaySDK\OAuth\Services;
use App\Setting;

use function HighlightUtilities\getAvailableStyleSheets;

class OAuthController extends Controller
{
    public $credentials;
    public $OAuthService;
    public $scope;
    public $token;
    public $marketPlaceId;
    public $config;


    /**
     * Gets a user OAuth Token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __construct(Request $request)
    {
        $settings = \App\Setting::where('groupName', 'ebay')
            ->get();
        $credential = [];
        $ebaysettings = [];
        foreach ($settings as $key => $value) {
            if (in_array($value->name, ['ruName', 'ebayMode', 'siteId'])) {
                $ebaysettings[$value->name] = $value->value;
                continue;
            }
            $credential[$value->name] = $value->value;
        }

        $this->scope = explode(' ', 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account');
        $this->marketPlaceId = \DTS\eBaySDK\Account\Enums\MarketplaceIdEnum::C_EBAY_US;
        $this->credentials =  $credential;


        $this->OAuthService = new \DTS\eBaySDK\OAuth\Services\OAuthService(
            [
                'credentials' => $this->credentials,
                'ruName' => $ebaysettings['ruName'],
            ]
        );
    }


    public function refreshauth(Request $request)
    {
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

    public function getauth(Request $request)
    {

        $url =  $this->OAuthService->redirectUrlForUser(
            [
                'state' => session('return'),
                'scope' => $this->scope,
            ]
        );

        return redirect()->away($url);
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



    public function getReturnUrl(string $returnURL = 'prev')
    {
        $return = '';
        if ($returnURL === 'prev') {

            $return = url()->current();
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
}
