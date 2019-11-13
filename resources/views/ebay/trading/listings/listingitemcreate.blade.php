@extends('layouts.app')
@push('head')
<style>
    .upload-image-drop-zone {
        height: 200px;
        border-width: 2px;
        margin-bottom: 20px;
    }

    /* skin.css Style*/
    .upload-image-drop-zone {
        color: #ccc;
        border-style: dashed;
        border-color: #ccc;
        text-align: center
    }

    .upload-image-drop-zone.drop {
        color: #222;
        border-color: #222;
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (Route::has('errors'))
            <div class="alert alert-danger route-errors" role="alert">
                <strong>{{ $errors->message }}</strong>
            </div>
            @endif
            <div class="container">
                <form method="POST" action="{{ route('trading/new') }}">
                @csrf
                @isset($Errors)
                <div class="alert alert-danger Errors" role="alert">
                    <strong>{{ $Errors['message'] }}</strong>
                </div>
                @endisset
                <h1 class="text-white">Creating Item</h1>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3 mt-3">
                            <div class="card-body">
                                <h4 class="card-title">eBay Item Title</h4>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Title</span>
                                    </div>
                                    <input maxlength="80" type="text" id="ebaytitle" class="form-control" placeholder="Item Title" aria-label="ItemTitle" aria-describedby="ebaytitle" value="">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="primaryCategory">Primary Category</label>
                                    <select id="primaryCategory" name="primaryCategory" class="form-control">
                                        <option selected>Choose...</option>
                                        <option value="1">Test 1</option>
                                        <option value="2">Test 2</option>
                                        <option value="3">Test 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <img id="item-image" src="../../images/details.jpg" class="ml-3 mt-3 right" alt="..." style="width:50px;">
                            <div class="card-body">
                                <h4 class="card-title">Item Details</h4>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">SKU</span>
                                    </div>
                                    <input maxlength="50" type="text" name="sku" id="sku" class="form-control" placeholder="Item SKU" aria-label="sku" aria-describedby="sku" value="">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">QTY</span>
                                    </div>
                                    <input type="number" name="qty" id="qty" min="1" max="9000" class="form-control" aria-label="qty" aria-describedby="qty" value="">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="price">Price $</span>
                                    </div>
                                    <input type="number" name="price" min=".01" max="9999999.00" step=".01" class="form-control" aria-label="price" aria-describedby="price" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Item Images</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        Main Image
                                                        <div class="button-group">
                                                            <input type="file" name="mainImageFile" class="form-control" id="mainImageFile">
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" id="del_main_image" name="del_main_image">
                                                                <span aria-hidden="true"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <img class="card-img" id="mainImage" src="..." alt="..." style="border: 2px dashed black">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        Description Image
                                                        <div class="button-group">
                                                            <input type="file" name="descriptionImageFile" class="form-control" id="descriptionImageFile">
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" id="del_description_image" name="del_description_image">
                                                                <span aria-hidden="true"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <img class="card-img" id="descriptionImage" src="..." alt="..." style="border: 2px dashed black">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <img id="item-image" src="../../images/shippingbox.png" class="ml-3 mt-3 right" alt="..." style="width:48px;">
                            <div class="card-body">
                                <h4 class="card-title">Shipping Details</h4>
                                <div class="form-row">
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="shippingHeight">Height</span>
                                            </div>
                                            <input type="number" name="shippingHeight" min="0" max="500" class="form-control" placeholder="0" aria-label="shippingHeight" aria-describedby="shippingHeight">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="shippingWidth">Width</span>
                                            </div>
                                            <input type="number" name="shippingWidth" min="0" max="500" class="form-control" placeholder="0" aria-label="shippingWidth" aria-describedby="shippingWidth">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="shippingLenght">Length</span>
                                            </div>
                                            <input type="number" name="shippingLenght" min="0" max="500" class="form-control" placeholder="0" aria-label="shippingLenght" aria-describedby="shippingLenght">
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="shippingCost">Shipping Cost $</span>
                                    </div>
                                    <input type="number" name="shippingCost" min=".01" max="9999999.00" step=".01" class="form-control" aria-label="shippingCost" aria-describedby="shippingCost">
                                </div>
                                @isset($ShippingPoliciesResponse->fulfillmentPolicies)
                                        <div class="form-group col-md-12">
                                            <label for="shippingPolicyProfile">Shipping Policy</label>
                                            <select id="shippingPolicyProfile" name="shippingPolicyProfile" class="form-control">
                                                <option selected>Choose...</option>
                                                @foreach($ShippingPoliciesResponse->fulfillmentPolicies as $FulfillmentPolicy )
                                                <option value="{{ $FulfillmentPolicy->fulfillmentPolicyId }}">{{ $FulfillmentPolicy->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                @endisset
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                   <h4 class="card-title">Business Policies</h4>
                                    
                                    <div class="form-group col-md-12" id="returns">
                                         
                                    </div>
                                   
                                    <div class="form-group col-md-12" id="payments">

                                        
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Description Output</h4>
                                <textarea id="descriptionEditorArea" name="descriptionEditorArea">
                                    <?php
                                    echo htmlentities($descriptionTemplate);
                                    ?>
                                </textarea>
                            </div>
                        </div>
                    </div>
               
               <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                        <h4 class="card-title">Submit Item</h4>
                            <div class="row">    
                                <div class="col-md-9">
                                    <div id="alertarea">
                                    </div>
                                    <div id="alertarea2" style="display:none;">
                                    </div>
                                    <div id="alertarea3" style="display:none;">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button id="preview" class="btn bg-primary text-white right" disabled>Preview</button>
                                    <button type="submit" id="savetoebay" class="btn bg-primary text-white right">Save Item</button>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>



            @endsection
            @push('end')
          

            <script src="{{ asset('js/createListing.js') }}"></script>

            @endpush
          