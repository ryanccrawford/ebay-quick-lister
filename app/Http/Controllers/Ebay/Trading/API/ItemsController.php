<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \Illuminate\Http\Request;
use Exception;
use \App\Http\Controllers\Ebay\Trading\EbayItemBaseController;

class ItemsController extends EbayItemBaseController
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
     * Display all listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->activeView($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate(
        //     [
        //         'mainImageFile' => 'required|image|mimes:jpeg,png,jpg,gif',
        //         'descriptionImageFile' => 'required|image|mimes:jpeg,png,jpg,gif'
        //     ]
        // );


        $imageName1 = $request->mainImageFile->store('images');
        $imageName2 = $request->descriptionImageFile->store('images');

        echo $imageName1;


        // $serviceRequest = new \DTS\eBaySDK\Trading\Types\VerifyAddFixedPriceItemRequestType();
        // $serviceRequest->Item = new \DTS\eBaySDK\Trading\Types\ItemType();
        // $serviceRequest->Item->AutoPay = true;
        // $serviceRequest->Item->ConditionID = 1000;
        // $serviceRequest->Item->Country = \DTS\eBaySDK\Trading\Enums\CountryCodeType::C_US;
        // $serviceRequest->Item->Currency = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        // $serviceRequest->Item->BuyItNowPrice = new \DTS\eBaySDK\Trading\Types\AmountType();
        // $serviceRequest->Item->BuyItNowPrice->currencyID = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        // $serviceRequest->Item->BuyItNowPrice->value = doubleval(trim($request->input('price')));
        // $serviceRequest->Item->Description =  trim($request->input('descriptionEditorArea'));
        // $serviceRequest->Item->DispatchTimeMax = 1;
        // $serviceRequest->Item->SKU =  trim($request->input('sku'));
        // $serviceRequest->Item->IncludeRecommendations = false;
        // $serviceRequest->Item->InventoryTrackingMethod = \DTS\eBaySDK\Trading\Enums\InventoryTrackingMethodCodeType::C_SKU;
        // $serviceRequest->Item->ListingType = \DTS\eBaySDK\Trading\Enums\ListingTypeCodeType::C_FIXED_PRICE_ITEM;
        // $serviceRequest->Item->ListingDuration = \DTS\eBaySDK\Trading\Enums\ListingDurationCodeType::C_GTC;
        // $serviceRequest->Item->Location = "Ashland, VA";

        // $serviceRequest->Item->PictureDetails = new \DTS\eBaySDK\Trading\Types\PictureDetailsType();







        // $serviceRequest->Item->PictureDetails->PictureURL =  $imagePaths;

        // $serviceRequest->Item->PrimaryCategory = new \DTS\eBaySDK\Trading\Types\CategoryType();
        // $serviceRequest->Item->PrimaryCategory->CategoryID = $request->input('primaryCategory');
        // $serviceRequest->Item->ProductListingDetails = new \DTS\eBaySDK\Trading\Types\ProductListingDetailsType();
        // $serviceRequest->Item->ProductListingDetails->BrandMPN = new \DTS\eBaySDK\Trading\Types\BrandMPNType();
        // $serviceRequest->Item->ProductListingDetails->BrandMPN->Brand = "3 Star Inc";
        // $serviceRequest->Item->ProductListingDetails->BrandMPN->MPN = $request->input('sku');
        // $serviceRequest->Item->Quantity = intval($request->input('qty'));



        //$serviceResponse = $this->getService('verifyAddFixedPriceItem', ($serviceRequest));
        // if ($serviceResponse->Ack === 200) {
        //  $serviceRealRequest = new \DTS\eBaySDK\Trading\Types\AddFixedPriceItemRequestType();
        //  $serviceRealRequest->Item = $serviceRequest->Item;

        //   $serviceRealResponse = $this->service->addFixedPriceItem($serviceRealRequest);
        //   if ($serviceRealResponse->Ack === 200) {
        // $SellerItem = new \App\SellerItem();
        // $SellerItem->title = $request->input('title');
        // $SellerItem->price = doubleval($request->input('price'));
        // $SellerItem->sku = $request->input('sku');
        // $SellerItem->descriptionEditorArea = $request->input('descriptionEditorArea');
        // $SellerItem->ShippingPoliciesResponse = intval($request->input('ShippingPoliciesResponse'));
        // $SellerItem->ReturnPoliciesResponse = intval($request->input('ReturnPoliciesResponse'));
        // $SellerItem->shippingCost = doubleval($request->input('shippingCost'));
        // $SellerItem->shippingLength = intval($request->input('shippingLength'));
        // $SellerItem->shippingWidth = intval($request->input('shippingWidth'));
        // $SellerItem->shippingHeight = intval($request->input('shippingHeight'));
        // $SellerItem->shippingWeight = intval($request->input('shippingWeight'));
        // $SellerItem->primaryCategory = intval($request->input('primaryCategory'));
        // $SellerItem->mainImageFile =  $imageName1;
        // $SellerItem->descriptionImageFile = $imageName2;
        // $SellerItem->save();

        return redirect()->route('trading/edit')
            ->with($request->input);
        // }
        //  }

        //   return redirect()->route('trading/edit', ['create' => 'true'])->withInput($request->input);

    }



    /**
     * Display one specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $item_id = $request->query('item_id');
        $create = $request->query('create');
        if ($create === 'true') {
            $descriptionTemplate = file_get_contents(public_path() . '/files/policy.html');
            try {
                if ($this->AccountService === null) {

                    $this->AccountService = new \DTS\eBaySDK\Account\Services\AccountService(
                        [
                            'siteId' => '0',
                            'authorization' => session('user_token'),
                            'credentials' => $this->credentials,
                        ]
                    );
                }
            } catch (Exception $e) {
                $this->doOAuth($request->fullUrl());
                return redirect('getauth');
            }
            return view('ebay.trading.listings.listingitemcreate', compact('descriptionTemplate', 'request'));
        }


        $itemResponse = $this->GetItemResponse($item_id);
        $item = $itemResponse->Item;

        if ($item instanceof \DTS\eBaySDK\Trading\Types\ItemType) {
            return view('ebay.trading.listings.listingitemedit', compact('item'));
        }
        $Errors = [
            'message' => 'Unknown Error. Can not view item ' . $item_id,

        ];

        if ($create === 'true') {


            return $this->retry($request, 'trading/edit?create=true', 'ebay.trading.listings.listingitemcreate');
        }
        return $this->retry($request, 'trading/edit?item_id=' . $item_id, 'ebay.trading.listings.listingitemedit');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
