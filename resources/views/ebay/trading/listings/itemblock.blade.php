
 @if($isActiveList)
                <input type="hidden" value="{{ url('/api/quickupdate/price') }}" id="priceupdateurl">
                <input type="hidden" value="{{ url('/api/quickupdate/qoh') }}" id="qohupdateurl">
<div class="table-responsive">
                <table class="table table-striped table-hover">
    <thead class="thead-dark">
    <tr>
      <th scope="col">Item ID</th>
      <th scope="col">Image</th>
      <th scope="col">Title</th>
      <th scope="col">SKU</th>
      <th scope="col">Price</th>
      <th scope="col">Update Price</th>
      <th scope="col">Quantity</th>
      <th scope="col">Update Quantity</th>
      <th scope="col">Watchers</th>
      <th scope="col">Edit Item</th>
      <th scope="col">View on Ebay</th>
      <th scope="col">Update Messages</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($itemsArray as $listingItem)


    <tr>
      <th scope="row"><span class="badge badge-secondary ml-2">{{$listingItem->ItemID}}</span></th>
      <td><img src="{{ $listingItem->PictureDetails->GalleryURL }}" alt="photo"></td>
      <td>{{ $listingItem->Title }}</td>
      <td><span class="badge badge-secondary">{{ $listingItem->SKU }}</span></td>
      <td><span id="current_price_{{ $listingItem->ItemID }}">
                                                @php
                                                    $number = floatval($listingItem->BuyItNowPrice->value );
                                                @endphp
                                                ${{  number_format($number, 2) }}
                                            </span></td>
                                          <td> <input type="number" name="price_{{$listingItem->ItemID}}" id="price_{{$listingItem->ItemID}}" min="0.00" max="999999.99" class="form-control text-right" aria-label="Dollar amount (with dot and two decimal places)" value="{{number_format($number, 2) }}">
                                        <span class="input-group-append">
                                            <button class="btn btn-info text-white small change-price" type="button" id="change_price_{{ $listingItem->ItemID }}" data-item="{{$listingItem->ItemID}}" name="change_price_{{$listingItem->ItemID}}">Update</button>
                                        </span></td>
                                            <td><span id="current_qoh_{{$listingItem->ItemID}}">
                                                    @php
                                                        $qty = intval($listingItem->QuantityAvailable);
                                                    @endphp
                                                    {{  number_format($qty, 0) }}
                                                </span></td>
                                                <td><input type="number" min="0" max="9999" id="qoh_{{$listingItem->ItemID}}" name="qoh_{{$listingItem->ItemID}}" placeholder="0" class="form-control text-right" aria-label="Quantity on Hand" value="{{number_format($qty, 0)}}">

                                                        <button class="btn btn-info text-white change-qoh" type="button" id="change_qoh_{{$listingItem->ItemID}}" data-item="{{$listingItem->ItemID}}" name="change_qoh_{{$listingItem->ItemID}}">Update</button>
                                                    </td>

                                        <td><span class="badge badge-primary round ml-2" data-toggle="tooltip" data-placement="top" title="Watchers"><i class="fa fa-eye" aria-hidden="true"></i> {{$listingItem->WatchCount}}</span></td>

                                                     <td><a class="btn btn-secondary btn-sm" href="{{$listingItem->ListingDetails->ViewItemURL}}" data-toggle="tooltip" data-placement="top" title="View on Ebay"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                      </td>
                                                     <td> <a href="{{ route('trading/edit') . '?item_id=' . $listingItem->ItemID }}" class="btn btn-primary btn-sm text-right" data-toggle="tooltip" data-placement="top" title="Edit Listing"><i class="fa fa-pencil" aria-hidden="true"></i></a>
</td>
                                                    <td><div id="error_{{ $listingItem->ItemID}}" class="alert alert-danger alert-dismissible fade show" style="display:none;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></td>

    </tr>
                    @endforeach
      </tbody>
</table>
</div>
                    @endif

            @if($isSoldList)
            @foreach ($itemsArray as $OrderTransaction)
              <div class="table-responsive" >
                    <table class="table table-striped table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Date</th>
                            <th scope="col">Buyer</th>
                            <th scope="col">Zip</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $transaction = $OrderTransaction->Transaction;
                        ?>

                    <tr>
                            <th scope="row"><span class="badge badge-secondary ml-2">{{  $transaction->Item->ItemID}}</span></td>
                            <td><span class="badge badge-primary">{{ date_format($transaction->CreatedDate, "F jS ,Y") }}</span></td>
                            <td><span class="badge badge-secondary ml-2">{{ $transaction->Buyer->Email }}</span></td>
                            <td>{{ $transaction->Buyer->BuyerInfo->ShippingAddress->PostalCode }}</td>
                            <td><a class="btn btn-secondary btn-sm" href="{{ $transaction->Item->ListingDetails->ViewItemURL}}" data-toggle="tooltip" data-placement="top" title="View on Ebay"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapseExample_{{  $transaction->Item->ItemID }}" aria-expanded="false" aria-controls="collapseExample_{{  $transaction->Item->ItemID }}">+</button></td>
                    </tr>
                        <tr>
                            <td colspan="5">
                            <table class="table table-sm">
                                   <thead class="thead-light">
                                   <tr>
                                      <th scope="col">Image</th>
                                      <th scope="col">SKU</th>
                                      <th scope="col">QTY</th>
                                      <th scope="col">Price</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                       <td><img src="{{ $transaction->Item->PictureDetails->GalleryURL }}" style="width:64px;" alt="photo"></th>
                                       <td>{{  $transaction->Item->SKU }}</td>
                                       <td>{{ $transaction->QuantityPurchased }}</td>
                                       <td>$ {{ $transaction->Item->BuyItNowPrice->value }}</td>
                                    </tr>
                                        </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                    @endforeach


            @endif




