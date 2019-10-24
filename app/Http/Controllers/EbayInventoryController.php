<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use \DTS\eBaySDK\Inventory;
use \DTS\eBaySDK;
use \DTS\eBaySDK\OAuth;
use DTS\eBaySDK\Credentials\Credentialsl;
use App\User;
use \App\inventoryPart;
use Illuminate\Foundation\Validation\ValidatesRequests;

class EbayInventoryController extends Controller
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
        $this->doCreateOAuth();
    }

    /**
     * Do OAuth.
     *
     * @return void
     */
    public function doCreateOAuth()
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

    /**
     * Get ebay Token
     *
     * @return Illuminate\Routing\Redirector
     */
    public function getToken()
    {
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

    /**
     * Starts SDK Service.
     *
     * @return void
     */
    public function startService()
    {
        if (!isset($this->inventoryService)) {
            $this->inventoryService = new \DTS\eBaySDK\Inventory\Services\InventoryService(
                [
                    'authorization' => $this->token
                ]
            );
        }
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
            $inventoryItems = $this->getInventoryItems();
            return view('ebay.listings', compact('inventoryItems'));
        } else {
            return $this->getToken();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showlocations(Request $request)
    {
        if ($request->session()->has('token')) {
            $this->token = session('token');
            $inventoryLocations = $this->getInventoryLocations();
            return view('ebay.inventory.locationlist', compact('inventoryLocations'));
        } else {
            return $this->getToken();
        }
    }

    /**
     * Show the Create Form to create a new Inventory item.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('ebay.create');
    }

    /**
     * Gets all Users eBay Inventory Items.
     *
     * @return \DTS\eBaySDK\Inventory\Types\InventoryItems
     */
    public function getInventoryItems()
    {
        $this->startService();
        $request = new \DTS\eBaySDK\Inventory\Types\GetInventoryItemsRestRequest();
        $response = $this->inventoryService->getInventoryItems($request);
        return $response->inventoryItems;
    }


    /**
     * Show the Create Form to create a new Inventory Location.
     *
     * @return \Illuminate\Http\Response
     */
    public function createInventoryLocation(Request $request)
    {
        return view('ebay.inventory.locationadd');
    }

    /**
     * Show the Create Form to create a new Inventory Location.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveInventoryLocation(Request $request)
    {
        //
        
        // $validatedData = $request->validate(
        //     [
        //         'name' => 'required|max:1000',
        //         'locationTypes' => 'required',
        //         'addressLine1' => 'required',
        //         'addressLine2' => 'required',
        //         'city' => 'required',
        //         'state' => 'required',
        //         'postalCode' => 'required',
        //     ]
        // );
        if ($request->session()->has('token')) {
            $this->token = session('token');
            echo var_dump($request->data);
            return response()->json(
                [
                    'message' => $request->data
                ]
            );
            $locationdata = $request->data;
            $inventoryLocation = new \DTS\eBaySDK\Inventory\Types\InventoryLocation();
            $inventoryLocation->name = $locationdata->name;
            $inventoryLocation->location = new \DTS\eBaySDK\Inventory\Types\Location();
            $inventoryLocation
                ->location
                ->address = new Address();
            $inventoryLocation
                ->location
                ->address
                ->addressLine1 =  $locationdata->addressLine1;
            $inventoryLocation
                ->location
                ->address
                ->addressLine2 = $locationdata->addressLine2;
            $inventoryLocation
                ->location
                ->address
                ->city = $locationdata->city;
            $inventoryLocation
                ->location
                ->address
                ->country = CountryCodeEnum::C_US;
            $inventoryLocation
                ->location
                ->address
                ->postalCode = $locationdata->postalCode;
            $inventoryLocation
                ->location
                ->address
                ->stateOrProvince = $locationdata->state;
            $inventoryLocation->locationTypes = [];
            $locationTypes = $locationdata->locationTypes;
            foreach ($locationTypes as $locationType) {
                if ($locationType === 'C_STORE') {
                    $inventoryLocation
                        ->locationTypes[] = StoreTypeEnum::C_STORE;
                }
                if ($locationType === 'C_WAREHOUSE') {
                    $inventoryLocation
                        ->locationTypes[] = StoreTypeEnum::C_WAREHOUSE;
                }
            }
            echo var_dump($inventoryLocation);
            $ebayRequest = new \DTS\eBaySDK\Inventory\Types\CreateInventoryLocationRestRequest();
            $ebayRequest->location = $inventoryLocation;
            $ebayRequest->merchantLocationKey = substr(str_replace(" ", "", $inventoryLocation->name), 0, 35);
            $ebayResponse = $this->inventoryService->createInventoryLocation($request);


            return redirect()
                ->action(
                    'App\Http\Controllers\EbayInventoryController@showlocations'
                );
        }

        return $this->getToken();
    }

    /**
     * Gets all Users eBay Inventory Locations.
     *
     * @return \DTS\eBaySDK\Inventory\Types\InventoryLocations
     */
    public function getInventoryLocations()
    {
        $this->startService();
        $request = new \DTS\eBaySDK\Inventory\Types\GetInventoryLocationsRestRequest();
        $request->offset = "0";
        $request->limit = "100";
        $response = $this->inventoryService->getInventoryLocations($request);
        echo var_dump($response->locations);
        return $response->locations;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function migrateListing(Request $request)
    {
        if ($request->session()->has('token')) {
            $TradingService = new \DTS\eBaySDK\Trading\Services\TradingService(
                [
                    'globalId' => \DTS\eBaySDK\Constants\GlobalIds::US,
                    'credentials' => EbayInventoryController::env(),
                ]
            );


            $tradingRequest = new \DTS\eBaySDK\Trading\Types\Item;





            $this->token = session('token');

            $listingIds = [];

            $Inventoryservice = new \DTS\eBaySDK\Inventory\Services\InventoryService(
                [
                    'authorization' => $this->token
                ]
            );
            $BulkMigrateListingsRequest = new \DTS\eBaySDK\Inventory\Types\BulkMigrateListingsRestRequest(
                [
                    'requests' => $listingIds,
                ]
            );

            $InventoryResponse = $Inventoryservice->bulkMigrateListings($BulkMigrateListingsRequest);

            $inventoryItems = $InventoryResponse->inventoryItems;
            return view('ebay.listings', compact('inventoryItems'));
        } else {
            return $this->__getToken();
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show(\App\Listing  $listing)
    {
        //
    }

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

    /**
     * Gets a user OAuth Token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

        return redirect('home');
    }

    /**
     * Gets eBay Credentials from Env Vars.
     *
     * @return \DTS\eBaySDK\Credentials\Credentials
     * @throws InvalidArgumentException
     */
    public static function env()
    {
        // This function IS the credentials provider.
        return function () {
            // Use credentials from environment variables, if available
            $appId = getenv(self::EBAY_PROD_APP_ID);
            $certId = getenv(self::EBAY_PROD_CERT_ID);
            $devId = getenv(self::EBAY_PROD_DEV_ID);

            if ($appId && $certId && $devId) {
                return new \DTS\eBaySDK\Credentials\Credentials($appId, $certId, $devId);
            } else {
                return new InvalidArgumentException(
                    'Could not find environment variable '
                        . 'credentials in ' . self::EBAY_PROD_APP_ID . '/'
                        . self::EBAY_PROD_CERT_ID . '/'
                        . self::EBAY_PROD_DEV_ID
                );
            }
        };
    }
}
