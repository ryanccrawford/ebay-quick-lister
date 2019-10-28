@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (Route::has('errors'))
            <div class="alert alert-danger" role="alert">
                <strong>{{ print_r($errors, true) }}</strong>
            </div>
            @endif
            <div class="container">
                <div class="row justify-content-center ">
                <span><h1 class="mt-1 mb-1">Listings<a class="btn btn-danger round" href="{{ route('inventory/showlocationadd') }}">{{ __('Add') }}</a></h1></span>

                <div class="col-sm-12 mt-3">
                    @foreach ($listingItems as $listingItem)
                        <div class="card shadow rounded mb-3" style="max-width: 450px;">
                        <div class="row no-gutters">
                           <img src="{{$listingItem->image[0]}}" class="card-img-top" alt="eBay Warehouse Location">
                              <div class="col-md-6">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $listingItem->title }}</h5>
                                        <p class="card-text">Price: {{ $listingItem->price}}<br></p>
                                        <p class="card-text strong">
                                            Description:<br>
                                        {{ $listingItem->description }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col">
                                <div class="card-body">
                                <p class="card-text">
                                <a href="#" class="btn btn-primary btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                </p>
                                </div>
                                </div>
                            </div>
                            </div>
                        @endforeach
                </div>
            </div>
    @endsection
@push('end')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
@endpush
