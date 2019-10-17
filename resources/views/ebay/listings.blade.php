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
            <div class="panel">
                <div class="panel-header">Seller Listings</div>
                <div class="panel-body">
                <div class="container">
                    @foreach ($inventoryItems as $inventoryItem)
                        <p>{{ $inventoryItem->sku }}</p>
                    @endforeach
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
