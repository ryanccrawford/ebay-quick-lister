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
                <div class="row justify-content-center">
                    <div class="col s12">
                        <span><h1 class="mt-1 mb-1">Inventory Locations <a class="btn btn-danger round" href="{{ route('inventory/locations/add') }}">{{ __('Add') }}</a></h1></span>
                        @foreach ($inventoryLocations as $inventoryLocation)
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $inventoryLocation->name }}</h5>
                                <p class="card-text">Address</p>
                                <div class="card-text">{{ $inventoryLocation->location->address->addressLine1 }}</div>
                                <div class="card-text">{{ $inventoryLocation->location->address->addressLine2 }}</div>
                                <div class="card-text">{{ $inventoryLocation->location->address->city }} {{ $inventoryLocation->location->address->stateOrProvince }}, {{ $inventoryLocation->location->address->postalCode }}</div>
                                <p class="card-text"></p>
                                <p class="card-text"></p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


                @endsection
