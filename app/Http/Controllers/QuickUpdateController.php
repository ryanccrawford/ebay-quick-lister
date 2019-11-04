<?php

namespace App\Http\Controllers;

use DTS\eBaySDK\Trading\Types\InventoryStatusType;
use Illuminate\Http\Request;

class QuickUpdateController extends Controller
{

    protected $Service;
    protected $changes = [];

    public function __construct()
    {
        $this->Service = new \DTS\eBaySDK\Trading\Services\TradingService();
    }


    public function price(Request $request)
    {

        $newPrice = $request->query('price');
        $itemId = $request->query('item_id');
        $change = [
            'price' => $newPrice,
            'itemId' => $itemId,
        ];
    }

    public function qoh(Request $request)
    {

        $newQoh = $request->query('qoh');
        $itemId = $request->query('item_id');
        $change = [
            'qoh' => $newQoh,
            'itemId' => $itemId,
        ];
    }

    public function update($changes)
    {
        $changeRequest = new \DTS\eBaySDK\Trading\Types\ReviseInventoryStatusRequestType();
        $changeRequest->InventoryStatus = new InventoryStatusType();
        $changeRequest->InventoryStatus->ItemID = $changes['itemId'];
        if (isset($changes['qoh'])) {
            $changeRequest->InventoryStatus->Quantity = intval($changes['qoh']);
        }
        if (isset($changes['price'])) {
            $changeRequest->InventoryStatus->StartPrice = intfloat($changes['price']);
        }
    }
}
