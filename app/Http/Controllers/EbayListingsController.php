<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use \DTS\eBaySDK;
use \DTS\eBaySDK\OAuth;
use DTS\eBaySDK\Credentials\Credentialsl;
use App\User;
use DTS\eBaySDK\Inventory\Types\InventoryItems;

class EbayListingsController extends Controller
{
    public $configuation;
    public $provider;
    public $inventoryService;
    public $OAuthService;
    public $credentials;
    public $token = '';
    public $code = '';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->session()->has('token')) {
            $this->token = session('token');

            $service = new \DTS\eBaySDK\Inventory\Services\InventoryService(
                [
                    'authorization' => $this->token
                ]
            );
            $request = new \DTS\eBaySDK\Inventory\Types\GetInventoryItemsRestRequest();
            $response = $service->getInventoryItems($request);

            $inventoryItems = $response->inventoryItems;
            return view('ebay.listings', compact('inventoryItems'));
        }

        if ($this->token == '') {
            $url =  $this->OAuthService->redirectUrlForUser(
                [
                    'state' => 'bar',
                    'scope' => [
                        'https://api.ebay.com/oauth/api_scope',
                        'https://api.ebay.com/oauth/api_scope/sell.inventory',
                        'https://api.ebay.com/oauth/api_scope/sell.fulfillment'
                    ]
                ]
            );
            return redirect()->away($url);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { }

    /**
     * Display the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show()
    { }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function edit(Listing $listing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Listing $listing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listing $listing)
    {
        //
    }

    public function oauth(Request $request)
    {
        $uri = $request->path();
        if ($request->is('oauth/clear')) {
            $request->session()->forget('token');
            return redirect('home');
        }
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
                $this->token = $response->access_token;
                session(['token' => $this->token]);
                //$affected = DB::update('update users set ebay_token = "' . $this->token . '" where name = ?', ['John']);
                return redirect('listings');
            }
        }
    }

    public static function env()
    {
        // This function IS the credentials provider.
        return function () {
            // Use credentials from environment variables, if available
            $appId = getenv(self::EBAY_PROD_APP_ID);
            $certId = getenv(self::EBAY_PROD_CERT_ID);
            $devId = getenv(self::EBAY_PROD_DEV_ID);

            if ($appId && $certId && $devId) {
                return new Credentials($appId, $certId, $devId);
            } else {
                return new \InvalidArgumentException(
                    'Could not find environment variable '
                        . 'credentials in ' . self::EBAY_PROD_APP_ID . '/'
                        . self::EBAY_PROD_CERT_ID . '/'
                        . self::EBAY_PROD_DEV_ID
                );
            }
        };
    }
}
