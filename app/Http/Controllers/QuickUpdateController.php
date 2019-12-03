<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Ebay\Trading\EbayBaseController;
use DTS\eBaySDK\Trading\Types\InventoryStatusType;
use Illuminate\Http\Request;

class QuickUpdateController extends EbayBaseController
{

    protected $Service;
    protected $changes = [];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Service = new \DTS\eBaySDK\Trading\Services\TradingService();
    }


    public function price(Request $request)
    {

        $newPrice = $request->query('price');
        $itemId = $request->query('item_id');
        $this->change = [
            'price' => $newPrice,
            'itemId' => $itemId,
        ];
    }

    public function qoh(Request $request)
    {

        $newQoh = $request->query('qoh');
        $itemId = $request->query('item_id');
        $this->change = [
            'qoh' => $newQoh,
            'itemId' => $itemId,
        ];
    }

    public function update($changes)
    {
        $this->middleware('auth');
        $this->middleware('ebayauth');
        $changeRequest = new \DTS\eBaySDK\Trading\Types\ReviseInventoryStatusRequestType();
        $changeRequest->InventoryStatus = new InventoryStatusType();
        $changeRequest->InventoryStatus->ItemID = $this->changes['itemId'];
        if (isset($this->changes['qoh'])) {
            $changeRequest->InventoryStatus->Quantity = intval($this->changes['qoh']);
        }
        if (isset($this->changes['price'])) {
            $changeRequest->InventoryStatus->StartPrice = doubleval($this->changes['price']);
        }
    }
}
