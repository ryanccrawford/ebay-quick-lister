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
                <span><h1 class="mt-1 mb-1">Inventory Locations <a class="btn btn-danger round" href="{{ route('inventory/showlocationadd') }}">{{ __('Add') }}</a></h1></span>

                <div class="col-sm-12 mt-3">
                    @foreach ($inventoryLocations as $inventoryLocation)
                        <div class="card shadow rounded mb-3" style="max-width: 450px;">
                        <div class="row no-gutters">
                        @for($i = 0; $i < count($inventoryLocation->locationTypes); $i++)
                            <div class="col-md-3 pt-3 pl-3">
                                @if(  $inventoryLocation->locationTypes[$i] === 'WAREHOUSE' )
                                <img src="../../images/warehouse.png" class="card-img align-middle" alt="eBay Warehouse Location">
                                @endif
                                @if( $inventoryLocation->locationTypes[$i] === 'STORE' )
                                <img src="../../images/warehouse.png" class="card-img align-middle" alt="eBay Warehouse Location">
                                @endif
                            </div>
                        @endfor
                                <div class="col-md-6">
                                <div class="card-body">
                                    <h5 class="card-title">Location: {{ $inventoryLocation->name }}</h5>
                                    <p class="card-text strong">
                                        Address:<br>
                                        {{ $inventoryLocation->location->address->addressLine1 }}<br>
                                        @if(strlen($inventoryLocation->location->address->addressLine2))
                                        {{ $inventoryLocation->location->address->addressLine2 }}<br>
                                        @endif
                                        {{ $inventoryLocation->location->address->city }} {{ $inventoryLocation->location->address->stateOrProvince }}, {{ $inventoryLocation->location->address->postalCode }}
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
@push('start')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
@endpush
