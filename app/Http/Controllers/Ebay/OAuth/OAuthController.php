<?php

namespace App\Http\Controllers\Ebay\OAuth;

use App\Http\Controllers\Controller;
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


  }

   public function getauth(Request $request)
  {
      $scope = $request->session('scope');

        $url =  $this->OAuthService->redirectUrlForUser(
            [
                'state' => 'bar',
                'scope' => $scope
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
            $rd =   session('return');
            return redirect($rd);
        }
        return redirect('home');
  }
