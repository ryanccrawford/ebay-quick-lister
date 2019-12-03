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
use \App\Http\Controllers\Ebay\Trading\EbayBaseController;

class EbayInventoryController extends EbayBaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->middleware('auth');
        $inventoryItems = $this->getInventoryItems();
        return view('ebay.inventory.items.itemlist', compact('inventoryItems'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showlocations(Request $request)
    {
        $this->middleware('auth');
        $inventoryLocations = $this->getInventoryLocations();
        return view('ebay.inventory.locations.locationlist', compact('inventoryLocations'));
    }

    /**
     * Show the Create Form to create a new Inventory item.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    { }

    /**
     * Gets all Inventory Items.
     *
     * @return \DTS\eBaySDK\Inventory\Types\InventoryItems
     */
    public function getInventoryItems()
    {
        $this->middleware('ebayauth');
        $serviceRequest = new \DTS\eBaySDK\Inventory\Types\GetInventoryItemsRestRequest();
        $response = $this->getService('getInventoryItems', $serviceRequest,  $apiName = "Inventory", $serviceType = "InventoryService");
        dump($response);
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
        $this->middleware('ebayauth');
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
        $ebayResponse = $this->getService('createInventoryLocation', $ebayRequestObj,  $apiName = "Inventory", $serviceType = "InventoryService");

        $responseArray = $ebayResponse->toArray();

        $wasError = false;
        $statusCode = $ebayResponse->getStatusCode();
        //Check for errors
        if ($statusCode >= 400) {
            $wasError = true;
            if (isset($responseArray['errors'])) {
                $errorMessage = '';
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
        $this->middleware('ebayauth');
        $serviceRequest = new \DTS\eBaySDK\Inventory\Types\GetInventoryLocationsRestRequest();
        $serviceRequest->offset = "0";
        $serviceRequest->limit = "100";
        $response = $this->getService('getInventoryLocations', $serviceRequest,  $apiName = "Inventory", $serviceType = "InventoryService");
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
}
