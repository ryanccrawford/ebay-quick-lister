<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Ebay\Trading\EbayBaseController;
use DTS\eBaySDK\Trading\Types\InventoryStatusType;
use Illuminate\Http\Request;
use App\SellerItem;
use \DTS\eBaySDK\Types\RepeatableType;


class QuickUpdateController extends EbayBaseController
{


    protected $changes = [];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


    public function price(Request $request)
    {
        $this->middleware('auth');
        $newPrice = $request->query('price');
        $itemId = $request->query('item_id');
        $this->changes = [
            'price' => doubleval($newPrice),
            'item_id' => $itemId,
        ];
        return $this->update($request);
    }

    public function qoh(Request $request)
    {
        $this->middleware('auth');
        $newQoh = $request->query('qoh');
        $itemId = $request->query('item_id');
        $this->changes = [
            'qoh' => intval($newQoh),
            'item_id' => $itemId,
        ];
        return $this->update($request);
    }

    public function update(Request $request)
    {
        
        $this->middleware('ebayauth');
        $changeRequest = new \DTS\eBaySDK\Trading\Types\ReviseInventoryStatusRequestType();
       
       
        $serviceRsponse = new \DTS\eBaySDK\Trading\Types\ReviseInventoryStatusResponseType();

        if (isset($this->changes['qoh'])) {
            
            $inventory = new \DTS\eBaySDK\Trading\Types\InventoryStatusType();
            $inventory->ItemID = $this->changes['item_id'];
            $inventory->Quantity = $this->changes['qoh'];
           
    
            $changeRequest->InventoryStatus[] =  $inventory;
            $serviceRsponse = $this->getService('ReviseInventoryStatus', $changeRequest, 'Trading', 'TradingService');
            
            if ($serviceRsponse->Ack === 'Success') {
                $this->changes['ebay'] = true;
                $dbresult = \App\SellerItem::where('ebayItemId', $this->changes['item_id'])->update(['qty' => $this->changes['qoh']]);
            } else {
                $this->changes['ebay'] = false;
            }
        }
        if (isset($this->changes['price'])) {

            $inventory = new \DTS\eBaySDK\Trading\Types\InventoryStatusType();
            $inventory->ItemID = $this->changes['item_id'];
            $inventory->StartPrice = $this->changes['price'];
           
    
            $changeRequest->InventoryStatus[] =  $inventory;

         
            $serviceRsponse = $this->getService('ReviseInventoryStatus', $changeRequest, 'Trading', 'TradingService');
            if ($serviceRsponse->Ack === 'Success') {
                $this->changes['ebay'] = true;
                $dbresult = \App\SellerItem::where('ebayItemId', $this->changes['item_id'])->update(['price' => $this->changes['price']]);
                
            } else {
                $this->changes['ebay'] = false;
            }
        }
        
        
        //return response()->json($serviceRsponse);
        return response()->json($this->changes);
    }
}
