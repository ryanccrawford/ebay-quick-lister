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
     * Verify a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(StoreItem $itemForm)
    {
        $this->middleware('auth');
        $this->middleware('ebayauth');
        $imageName1 = $itemForm->mainImageFile->store('images');
        $imageName2 = $itemForm->descriptionImageFile->store('images');
        $inputData = $itemForm->all();
        $temp = $inputData['descriptionEditorArea'];

        $temp = str_replace("@title", $imageName2, $temp);
        $temp = str_replace("@descriptionImage", $this->url->to('/') . $imageName2, $temp);
        $inputData['descriptionEditorArea'] =  $temp;

        $inputData['mainImageFile'] = $imageName1;
        $inputData['descriptionImageFile'] = $imageName2;
        $sku = array('sku' => $inputData['sku']);
        $item = new SellerItem();
        try {

            $item = SellerItem::updateOrCreate($sku, $inputData);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
        $verifiedItem = $this->GetVerifiedItem($item);
        $serviceRealRequest = new \DTS\eBaySDK\Trading\Types\AddFixedPriceItemRequestType();
        $serviceRealRequest->Item = $verifiedItem;

        $serviceRealResponse = $this->TradingService->addFixedPriceItem($serviceRealRequest);
        if ($serviceRealResponse->Ack === 200) {
            $itemAdded = $serviceRealRequest->Item;
            $ebay_item_id = $itemAdded->ItemID;
            $item->ebayItemId = $ebay_item_id;
            $item->save();
        }

        return redirect()->route('trading/edit', ['create' => 'true'])->withInput($itemForm->input);
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
        if ($item !== null) {
            return redirect()->route('trading/edit', ['create' => 'true'])->withInput($request->input);
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
