@extends('layouts.app')



@section('content')
<div class="fluid-container">

    <div class="row justify-content-center">
        <div class="col-md-10">

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
            <div class="fluid-container">
                @if($isActiveList)
                    <span><h1 class="mt-1 mb-2 left text-white">Listings<a class="btn btn-danger round ml-2" href="{{ route('trading/edit') . '?create=true' }}">{{ __('Add') }}</a></h1></span>
                @else
                    <h1 class="mt-1 mb-2 left text-white">Orders</h1>
                @endif
                <div class="row">
                <div class="col-sm-8">
                </div>

                <div class="col-sm-4">
                    <span>
                        <form class="form-inline my-2 my-lg-0 justify-content-end">
                    <input class="form-control mr-sm-2" type="search" placeholder="SKU" aria-label="Search">
                    <button class="btn btn-dark my-2 my-sm-0" type="submit">Find by SKU</button>
                    <div class="form-check">
                    @if($isActiveList)
                            <input class="form-check-input" type="radio" name="ActiveList" id="ActiveList" value="ActiveList" checked>
                            <label class="form-check-label mr-sm-1" for="ActiveList">Active Listings</label>
                            <input class="form-check-input" type="radio" name="ActiveList" id="SoldList" value="SoldList">
                            <label class="form-check-label mr-sm-1" for="SoldList">Sold Listings</label>
                    @else
                            <input class="form-check-input" type="radio" name="ActiveList" id="ActiveList" value="ActiveList">
                            <label class="form-check-label mr-sm-1" for="ActiveList">Active Listings</label>
                            <input class="form-check-input" type="radio" name="ActiveList" id="SoldList" value="SoldList" checked>
                            <label class="form-check-label mr-sm-1" for="SoldList">Sold Listings</label>
                    @endif
                        <input class="form-check-input" type="radio" name="UnsoldList" id="UnsoldList" value="UnsoldList" disabled>
                            <label class="form-check-label mr-sm-1" for="UnsoldList">Unsold Listings</label>
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
                @include('ebay.trading.listings.itemgroup')
            </div>
        @endisset


            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('end')
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    <script src="{{ asset('js/quickupdate.js') }}"></script>

@endpush
