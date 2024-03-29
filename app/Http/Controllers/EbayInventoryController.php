<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use \DTS\eBaySDK\Inventory;
use \DTS\eBaySDK\OAuth;
use DTS\eBaySDK\Credentials\Credentialsl;
use App\User;
use \App\inventoryPart;
use Illuminate\Foundation\Validation\ValidatesRequests;

class EbayInventoryController extends Controller
{
    protected $configuation;
    protected $provider;
    protected $inventoryService;
    protected $OAuthService;
    protected $credentials;
    protected $token = '';
    protected $code = '';

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
            return view('ebay.inventory.items.itemlist', compact('inventoryItems'));
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
            return view('ebay.inventory.locations.locationlist', compact('inventoryLocations'));
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
       
    }

    /**
     * Gets all Inventory Items.
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
        return view('ebay.inventory.locations.locationadd');
    }

    /**
     * Show the Create Form to create a new Inventory Location.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveInventoryLocation(Request $request)
    {
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
            $this->startService();
            $name = $request->name;
            $addressArray = array(
                'address' => array(
                    'addressLine1' => $request->addressLine1,
                    'addressLine2' => $request->addressLine2,
                    'city' => $request->city,
                    'country' => \DTS\eBaySDK\Inventory\Enums\CountryCodeEnum::C_US,
                    'postalCode' => $request->postalCode,
                    'stateOrProvince' => $request->state
                )
            );
            $locationDetails = new \DTS\eBaySDK\Inventory\Types\LocationDetails($addressArray);
            $locationTypesArray = $this->getLocationTypes($request->locationTypes);
            $location = array(
                'location' => $locationDetails,
                'locationTypes' => $locationTypesArray,
                'name' => $name,
            );
            $inventoryLocationFull = new \DTS\eBaySDK\Inventory\Types\InventoryLocationFull($location);
            $eBayRequest = array(
                'merchantLocationKey' => substr(str_replace(" ", "", $name), 0, 35),
                'location' => $locationDetails,
                'locationTypes' => $locationTypesArray,
                'name' => $name,
            );
            $ebayRequestObj = new \DTS\eBaySDK\Inventory\Types\CreateInventoryLocationRestRequest($eBayRequest);
            $ebayResponse = $this->inventoryService->createInventoryLocation($ebayRequestObj);
            $responseArray = $ebayResponse->toArray();

            $wasError = false;
            $statusCode = $ebayResponse->getStatusCode();
            //Check for errors
            if ($statusCode >= 400) {
                $wasError = true;
                if (isset($responseArray['errors'])) {
                    $errorMessage = '';
                    echo var_dump($responseArray['errors']);
                    foreach ($responseArray['errors'] as $rerror) {

                        if ($rerror['errorId'] === 25803) {
                            $errorMessage += "The inventory location name is already in use.<br>";
                        } else {
                            $errorMessage += $rerror['message'] . "<br>";
                        }
                    }
                    return view('ebay.inventory.locationadd', compact('wasError', 'request', 'errorMessage'));
                }
            }
            //Check for success
            if ($statusCode >= 200) {
                if ($statusCode === 204) {
                    return redirect()
                        ->action(
                            'EbayInventoryController@showlocations'
                        );
                }
            }
        }
        //Return getToken if totken has expired, Might need to handle this differnetly.
        return $this->getToken();
    }

    public function getLocationTypes($locationTypes)
    {
        $locationTypesArray = [];
        if (is_array($locationTypes)) {
            foreach ($locationTypes as $type) {
                if ($type === 'C_STORE') {
                    $locationTypesArray[] = \DTS\eBaySDK\Inventory\Enums\StoreTypeEnum::C_STORE;
                }
                if ($type === 'C_WAREHOUSE') {
                    $locationTypesArray[] = \DTS\eBaySDK\Inventory\Enums\StoreTypeEnum::C_WAREHOUSE;
                }
            }
        } else {
            if ($locationTypes === 'C_STORE') {
                $locationTypesArray[] = \DTS\eBaySDK\Inventory\Enums\StoreTypeEnum::C_STORE;
            }
            if ($locationTypes === 'C_WAREHOUSE') {
                $locationTypesArray[] = \DTS\eBaySDK\Inventory\Enums\StoreTypeEnum::C_WAREHOUSE;
            }
        }
        return $locationTypesArray;
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
