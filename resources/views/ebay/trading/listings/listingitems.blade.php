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
            <div class="row">
                <div class="col-sm-6">
            <span><h1 class="mt-1 mb-2 left">Listings<a class="btn btn-danger round ml-2" href="#">{{ __('Add') }}</a></h1></span>

                </div>
                <div class="col-sm-6">
                    <span>
               <form class="form-inline my-2 my-lg-0 justify-content-end">
                    <input class="form-control mr-sm-2" type="search" placeholder="SKU / Title" aria-label="Search">
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Search</button>
                </form>
                </div>
                </span>
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
                                            <input type="number" min="0.00" max="999999.99" placeholder="0.00" class="form-control text-right" aria-label="Dollar amount (with dot and two decimal places)">
                                        <div class="input-group-append">
                                            <button class="button btn-info text-white" type="button" id="change_price_{{$listingItem->ItemID}}">Update</button>
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
                                               <input type="number" min="0" max="9999" placeholder="0" class="form-control text-right" aria-label="Quantity on Hand">
                                                    <div class="input-group-append">
                                                        <button class="button btn-info text-white" type="button" id="change_qoh_{{$listingItem->ItemID}}">Update</button>
                                                    </div>
                                                </div>

                                                <div class="card-body">
                                                    <div class="btn-group-vertical">
                                                        <a class="btn btn-secondary btn-sm" href="{{$listingItem->ListingDetails->ViewItemURL}}" data-toggle="tooltip" data-placement="top" title="View on Ebay"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                        <a href="{{ route('trading/edit') . '?item_id=' . $listingItem->ItemID }}" class="btn btn-primary btn-sm text-right" data-toggle="tooltip" data-placement="top" title="Edit Listing"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                    </div>
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
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
