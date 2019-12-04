<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Ebay\Trading\EbayBaseController;
use DTS\eBaySDK\Trading\Types\InventoryStatusType;
use Illuminate\Http\Request;
use App\SellerItem;

class QuickUpdateController extends EbayBaseController
{


    protected $changes = [];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


    public function price(Request $request)
    {

        $newPrice = $request->query('price');
        $itemId = $request->query('item_id');
        $this->change = [
            'price' => $newPrice,
            'itemId' => $itemId,
        ];
        return $this->update($request);
    }

    public function qoh(Request $request)
    {

        $newQoh = $request->query('qoh');
        $itemId = $request->query('item_id');
        $this->change = [
            'qoh' => $newQoh,
            'itemId' => $itemId,
        ];
        return $this->update($request);
    }

    public function update(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('ebayauth');
        $changeRequest = new \DTS\eBaySDK\Trading\Types\ReviseInventoryStatusRequestType();
        $changeRequest->InventoryStatus = new InventoryStatusType();
        $changeRequest->InventoryStatus->ItemID = $this->changes['itemId'];
        $serviceRsponse = new \DTS\eBaySDK\Trading\Types\ReviseInventoryStatusResponseType();
        if (isset($this->changes['qoh'])) {
            $changeRequest->InventoryStatus->Quantity = intval($this->changes['qoh']);
            $serviceRsponse = $this->getService('ReviseInventoryStatus', $changeRequest, 'Trading', 'TradingService');
            if ($serviceRsponse->Ack === 'Success') {
                $this->changes['ebay'] = true;
                \App\SellerItem::where('ebayItemId', $this->change['itemId'])->update(['qty' => $this->changes['qoh']]);
            } else {
                $this->changes['ebay'] = false;
            }
        }
        if (isset($this->changes['price'])) {
            $changeRequest->InventoryStatus->StartPrice = doubleval($this->changes['price']);
            $serviceRsponse = $this->getService('ReviseInventoryStatus', $changeRequest, 'Trading', 'TradingService');
            if ($serviceRsponse->Ack === 'Success') {
                $this->changes['ebay'] = true;
                \App\SellerItem::where('ebayItemId', $this->change['itemId'])->update(['price' => $this->changes['price']]);
            } else {
                $this->changes['ebay'] = false;
            }
        }

        return response()->json($this->changes);
    }
}
