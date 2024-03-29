@extends('layouts.app')



@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (count($errors) > 0)
            <div class="alert alert-danger" role="alert">
                <strong>{{ $errors['messages'] }}</strong>
            </div>
            @endif
            @isset($Errors)
                @foreach($Errors as $error)
                    @foreach ($error as $key => $value)
                        <div class="alert alert-danger" role="alert">
                        <strong>{{ $key }} : {{ $value }}</strong>
                        </div>
                    @endforeach
                @endforeach
            @endisset
            <div class="container">
            <span><h1 class="mt-1 mb-2 left text-white">Listings<a class="btn btn-danger round ml-2" href="{{ route('trading/edit') . '?create=true' }}">{{ __('Add') }}</a></h1></span>

                <div class="row">
                <div class="col-sm-8">

                </div>
            <div class="col-sm-4">
                    <span>
               <form class="form-inline my-2 my-lg-0 justify-content-end">
                    <input class="form-control mr-sm-2" type="search" placeholder="SKU" aria-label="Search">
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Find by SKU</button>
                    <div class="form-check mb-2 mr-sm-2">
                        <input class="form-check-input" type="radio" name="ActiveList" id="ActiveList" value="ActiveList" checked>
                        <label class="form-check-label" for="inlineRadio1">Active Listings</label>
                    </div>
                    <div class="form-check mb-2 mr-sm-2">
                        <input class="form-check-input" type="radio" name="SoldList" id="SoldList" value="SoldList" disabled>
                        <label class="form-check-label" for="inlineRadio2">Sold Listings</label>
                    </div>
                    <div class="form-check mb-2 mr-sm-2">
                        <input class="form-check-input" type="radio" name="UnsoldList" id="UnsoldList" value="UnsoldList" disabled>
                        <label class="form-check-label" for="inlineRadio3">Unsold Listings</label>
                    </div>
                    <div class="form-check mb-2 mr-sm-2">
                        <input class="form-check-input" type="checkbox" name="IncludeVariations" id="IncludeVariations" disabled>
                        <label class="form-check-label" for="IncludeVariations">Include Variations</label>
                    </div>
                </form>
                    </span>
            </div>
                
            </div>
            @isset($itemsArray)
                @if($totalPages > 1)
                    <nav aria-label="pagination">
                        <div class="justify-content-center">
                                    <ul class="pagination justify-content-center">
                                    <li class="page-item"><a class="page-link" href="{{ $prev_link }}">Previous</a></li>


                                    @foreach ($beforeCurrentPageLinks as $link)

                                    <li class="page-item"><a class="page-link" href="{{ $link['link'] }}">{{ $link['page'] }}</a></li>

                                    @endforeach

                                    <li class="page-item active"><a class="page-link" href="#">{{ $currentPage }}</a></li>

                                    @foreach ($afterCurrentPageLinks as $link)

                                    <li class="page-item"><a class="page-link" href="{{ $link['link'] }}" >{{ $link['page'] }}</a></li>

                                    @endforeach

                                    <li class="page-item"><a class="page-link" href="{{ $next_link }}">Next</a></li>
                                </ul>

                        </div>

                    </nav>
                @endif
            <div class="col-lg-12">
                <div class="card-group">
                    <!-- { 'listingArray', 'next_link', 'prev_link', 'totalPages', 'limit', 'currentPage', 'afterCurrentPageLinks', 'beforeCurrentPageLinks' } -->
                    @foreach ($itemsArray as $listingItem)
                <div class="col-sm-6">
                    <div class="card shadow rounded mb-3">
                        <div class="row no-gutters">
                            <div class="col-sm-4">

                            <img src="{{ $listingItem->PictureDetails->GalleryURL }} " class="card-img pl-3 pt-3 pr-3" alt="photo">
                            </div>
                            <div class="col-sm-8">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $listingItem->Title }}</h6>
                                    <div class="badge badge-secondary">SKU: {{ $listingItem->SKU }}</div>
                                    <p class="badge badge-secondary ml-2">ID {{$listingItem->ItemID}}</p>
                                    <span class="badge badge-primary round ml-2" data-toggle="tooltip" data-placement="top" title="Watchers"><i class="fa fa-eye" aria-hidden="true"></i> {{$listingItem->WatchCount}}</span>
                                </div>
                                <div class="card-body">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-white"><strong>Price: </strong></span>
                                            <span class="input-group-text">
                                                @php
                                                    $number = floatval($listingItem->BuyItNowPrice->value );
                                                @endphp
                                                ${{  number_format($number, 2) }}
                                            </span>
                                        </div>
                                    <input type="number" name="price_{{$listingItem->ItemID}}" min="0.00" max="999999.99" placeholder="0.00" class="form-control text-right" aria-label="Dollar amount (with dot and two decimal places)" value="{{number_format($number, 2) }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-info text-white change-price" type="button" name="change_price_{{$listingItem->ItemID}}">Update</button>
                                        </div>
                                    </div>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-white"><strong>QOH: </strong></span>
                                                <span class="input-group-text">
                                                    @php
                                                        $qty = intval($listingItem->QuantityAvailable);
                                                    @endphp
                                                    {{  number_format($qty, 0) }}
                                                </span>
                                        </div>
                                    <input type="number" min="0" max="9999" name="qoh_{{$listingItem->ItemID}}" placeholder="0" class="form-control text-right" aria-label="Quantity on Hand" value="{{number_format($qty, 0)}}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-info text-white change-qoh" type="button" name="change_qoh_{{$listingItem->ItemID}}">Update</button>
                                                    </div>
                                                </div>


                                        </div>
                                        <div class="card-body">
                                                    <div class="btn-group">
                                                        <a class="btn btn-secondary btn-sm" href="{{$listingItem->ListingDetails->ViewItemURL}}" data-toggle="tooltip" data-placement="top" title="View on Ebay"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                        <a href="{{ route('trading/edit') . '?item_id=' . $listingItem->ItemID }}" class="btn btn-primary btn-sm text-right" data-toggle="tooltip" data-placement="top" title="Edit Listing"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                            </div>



                        </div>
                    </div>
                </div>
                        @endforeach
                    @endisset
                </div>

            </div>
            </div>
        </div>
    </div>

</div>
    @endsection

@section('end')
    <script src="../../js/quickupdate.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
