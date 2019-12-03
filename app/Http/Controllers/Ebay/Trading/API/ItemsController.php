<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \Illuminate\Http\Request;
use Exception;
use \App\Http\Controllers\Ebay\Trading\EbayBaseController;
use App\Http\Requests\StoreItem;
use App\SellerItem;
use DTS\eBaySDK\MerchantData\Enums\SiteCodeType;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ItemsController extends EbayBaseController
{

    protected $url;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, UrlGenerator $url)
    {
        parent::__construct($request);
        $this->url = $url;
    }

    /**
     * Display all listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('ebayauth');
        return $this->activeView($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItem $request)
    {
        $this->middleware('auth');
        $this->middleware('ebayauth');
        $item = null;
        try {
            $imageName1 = $request->mainImageFile->store('images');
            $imageName2 = $request->descriptionImageFile->store('images');
            $inputData = $request->all();
            $temp = $inputData['descriptionEditorArea'];

            $temp = str_replace("@title", $imageName2, $temp);
            $temp = str_replace("@descriptionImage", $this->url->to('/') . $imageName2, $temp);
            $inputData['descriptionEditorArea'] =  $temp;

            $inputData['mainImageFile'] = $imageName1;
            $inputData['descriptionImageFile'] = $imageName2;
            $sku = array('sku' => $request->sku);
            $item = SellerItem::updateOrCreate($sku, $inputData);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }

        $serviceRequest = new \DTS\eBaySDK\Trading\Types\VerifyAddFixedPriceItemRequestType();
        $serviceRequest->Item = new \DTS\eBaySDK\Trading\Types\ItemType();
        $serviceRequest->Item->AutoPay = true;
        $serviceRequest->Item->ConditionID = 1000;
        $serviceRequest->Item->Country = \DTS\eBaySDK\Trading\Enums\CountryCodeType::C_US;
        $serviceRequest->Item->Currency = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        $serviceRequest->Item->StartPrice = new \DTS\eBaySDK\Trading\Types\AmountType();
        $serviceRequest->Item->StartPrice->currencyID = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        $serviceRequest->Item->StartPrice->value = $item->price;
        $serviceRequest->Item->Description =  $item->descriptionEditorArea;
        $serviceRequest->Item->DispatchTimeMax = 1;
        $serviceRequest->Item->SKU =  $item->sku;
        $serviceRequest->Item->IncludeRecommendations = false;
        $serviceRequest->Item->InventoryTrackingMethod = \DTS\eBaySDK\Trading\Enums\InventoryTrackingMethodCodeType::C_SKU;
        $serviceRequest->Item->ListingType = \DTS\eBaySDK\Trading\Enums\ListingTypeCodeType::C_FIXED_PRICE_ITEM;
        $serviceRequest->Item->ListingDuration = \DTS\eBaySDK\Trading\Enums\ListingDurationCodeType::C_GTC;
        $serviceRequest->Item->Location = "Ashland, VA";
        $serviceRequest->Item->PictureDetails = new \DTS\eBaySDK\Trading\Types\PictureDetailsType();
        $imagePaths = [
            $this->url->to('/') . $imageName1,
            $this->url->to('/') . $imageName2
        ];

        $serviceRequest->Item->PictureDetails->PictureURL =  $imagePaths;
        $serviceRequest->Item->PrimaryCategory = new \DTS\eBaySDK\Trading\Types\CategoryType();
        $serviceRequest->Item->PrimaryCategory->CategoryID = $item->primaryCategory;
        $serviceRequest->Item->ProductListingDetails = new \DTS\eBaySDK\Trading\Types\ProductListingDetailsType();
        $serviceRequest->Item->ProductListingDetails->BrandMPN = new \DTS\eBaySDK\Trading\Types\BrandMPNType();
        $serviceRequest->Item->ProductListingDetails->BrandMPN->Brand = "3 Star Inc";
        $serviceRequest->Item->ProductListingDetails->BrandMPN->MPN = $item->sku;
        $serviceRequest->Item->Quantity = $item->qty;
        $serviceRequest->Item->ShippingPackageDetails = new \DTS\eBaySDK\Trading\Types\ShipPackageDetailsType();
        $serviceRequest->Item->ShippingPackageDetail->PackageDepth =  $item->shippingHeight;
        $serviceRequest->Item->ShippingPackageDetail->PackageLength = $item->shippingLength;
        $serviceRequest->Item->ShippingPackageDetail->PackageWidth  = $item->shippingWidth;
        $serviceRequest->Item->ShippingPackageDetail->WeightMajor = count(explode(".", floatval($item->shippingWeight))) > 0 ? explode(".", floatval($item->shippingWeight))[0] : 0;
        $serviceRequest->Item->ShippingPackageDetail->WeightMinor  = count(explode(".", floatval($item->shippingWeight))) > 0 ? explode(".", floatval($item->shippingWeight))[1] : 0;
        $serviceRequest->Item->SellerProfiles = new \DTS\eBaySDK\Trading\Types\SellerProfilesType();
        $serviceRequest->Item->SellerProfiles->SellerPaymentProfile = new \DTS\eBaySDK\Trading\Types\SellerPaymentProfileType();
        $serviceRequest->Item->SellerProfiles->SellerPaymentProfile->PaymentProfileID = $item->PaymentPoliciesResponse;
        $serviceRequest->Item->SellerProfiles->SellerReturnProfile = new \DTS\eBaySDK\Trading\Types\SellerReturnProfileType();
        $serviceRequest->Item->SellerProfiles->SellerReturnProfile->ReturnProfileID =  $item->ReturnPoliciesResponse;
        $serviceRequest->Item->SellerProfiles->SellerShippingProfile = new \DTS\eBaySDK\Trading\Types\SellerShippingProfileType();
        $serviceRequest->Item->SellerProfiles->SellerShippingProfile->ShippingProfileID =  $item->ShippingPoliciesResponse;
        $serviceRequest->Item->Site = SiteCodeType::C_US;
        $serviceRequest->Item->DispatchTimeMax = 1;

        $serviceResponse = $this->getService('verifyAddFixedPriceItem', ($serviceRequest));
        dump($serviceRequest);
        die;
        if ($serviceResponse->Ack === 200) {
            $serviceRealRequest = new \DTS\eBaySDK\Trading\Types\AddFixedPriceItemRequestType();
            $serviceRealRequest->Item = $serviceRequest->Item;

            $serviceRealResponse = $this->service->addFixedPriceItem($serviceRealRequest);
            if ($serviceRealResponse->Ack === 200) {
                $rto = route('trading/edit', ['create' => 'true']);
                return redirect($rto)
                    ->with($request->input);
            }
            //   return redirect()->route('trading/edit', ['create' => 'true'])->withInput($request->input);
        }
    }


    /**
     * Display one specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('ebayauth');
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
                $this->middleware('ebayauth');
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
        $this->middleware('auth');
        $this->middleware('ebayauth');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->middleware('auth');
        $this->middleware('ebayauth');
    }
}
