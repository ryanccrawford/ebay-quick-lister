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
            @if ($errors->any())
                <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </div>
            @endif
            <div class="container">
                <form enctype="multipart/form-data" method="POST" id="itemForm" name="itemForm">
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
                                    <input maxlength="80" name="title" type="text" id="ebaytitle" class="form-control" placeholder="Item Title" aria-label="ItemTitle" aria-describedby="ebaytitle" value="{{ old('title') ?? 'Test Antenna Mount'}}">
                                    @error('title')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <p>Categories are suggested based on the item title.</p>
                                <div class="form-group col-md-12" id="categorySuggestion">
                                    @error('primaryCategory')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button class="btn bg-primary text-white right" id="catsearchbutton">Get Suggestions <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="categorySpinner" style="display:none;"></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <img id="item-image" src="{{ $request->file('mainImageFileName') ?? '...'}}" class="ml-3 mt-3 right" alt="..." style="width:50px;">
                            <div class="card-body">
                                <h4 class="card-title">Item Details</h4>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">SKU</span>
                                    </div>
                                    <input maxlength="50" type="text" name="sku" id="sku" class="form-control" placeholder="Item SKU" aria-label="sku" aria-describedby="sku" value="{{old('sku') ?? 'MOUNT33433'}}">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">QTY</span>
                                    </div>
                                    <input type="number" name="qty" id="qty" min="1" max="9000" class="form-control" aria-label="qty" aria-describedby="qty" value="{{old('qty') ?? '100'}}">
                                    @error('qty')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="price">Price $</span>
                                    </div>
                                    <input type="number" name="price" min=".01" max="9999999.00" step=".01" class="form-control" aria-label="price" aria-describedby="price" value="{{old('price') ?? '123.00'}}">
                                    @error('price')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
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
                                                    <img class="card-img" id="mainImage" src="{{ $request->file('mainImageFile') ?? ''}}" alt="..." style="border: 2px dashed black">
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
                                                    <img class="card-img" id="descriptionImage" src="{{ $request->file('descriptionImageFile') ?? ''}}" alt="..." style="border: 2px dashed black">
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
                                                <span class="input-group-text" id="shippingWeight">Weight (lbs.)</span>
                                            </div>
                                            <input type="number" name="shippingWeight" min="0" max="500" class="form-control" placeholder="0" aria-label="shippingWeight" aria-describedby="shippingWidth" value="{{ old('shippingWeight') ?? '10' }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="shippingHeight">Height</span>
                                            </div>
                                            <input type="number" name="shippingHeight" min="0" max="500" class="form-control" placeholder="0" aria-label="shippingHeight" aria-describedby="shippingHeight" value="{{ old('shippingHeight') ?? '10' }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="shippingWidth">Width</span>
                                            </div>
                                            <input type="number" name="shippingWidth" min="0" max="500" class="form-control" placeholder="0" aria-label="shippingWidth" aria-describedby="shippingWidth" value="{{ old('shippingWidth') ?? '10'}}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="shippingLength">Length</span>
                                            </div>
                                            <input type="number" name="shippingLength" min="0" max="500" class="form-control" placeholder="0" aria-label="shippingLength" aria-describedby="shippingLength" value="{{ old('shippingLength') ?? '10' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="shippingCost">Shipping Cost $</span>
                                    </div>
                                    <input type="number" name="shippingCost" min=".01" max="9999999.00" step=".01" class="form-control" aria-label="shippingCost" aria-describedby="shippingCost" value="{{ old('shippingCost') ?? '1.00' }}">
                                </div>

                                        <div class="form-group col-md-12" id="shipping">
                                            <div class="text-center" id="shippingSpinner">
                                            <div class="spinner-border" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            </div>
                                        </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                   <h4 class="card-title">Business Policies</h4>

                                    <div class="form-group col-md-12" id="returns">
                                        <div class="text-center" id="returnsSpinner">
                                        <div class="spinner-border" role="status">
                                                <span class="sr-only">Loading...</span>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12" id="payments">
                                        <div class="text-center" id="paymentsSpinner">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        </div>
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
                                    if($request->old('descriptionEditorArea') != ''){
                                        echo $request->old('descriptionEditorArea');
                                    }else{
                                        echo htmlentities($descriptionTemplate);
                                    }

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
                                <div class="col-md-8">
                                    <div id="result">
                                    </div>
                                    <div id="alertarea2" style="display:none;">
                                    </div>
                                    <div id="alertarea3" style="display:none;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" id="savetoebay" class="btn bg-primary text-white right">Save For Later <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="verifySpinner" style="display:none;"></span></button>
                                    <button type="submit" id="saveforlater"  class="btn bg-primary text-white right">Verify and List <div class="progress" id="progress" style="display:none">
  <div class="progress-bar" id="uploadProgress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
</div></button>
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
            <?php
              $decodedhtml =  preg_replace( "/\r|\n|\r\n/", "", html_entity_decode($descriptionTemplate));
            ?>
            <script>
            
            var postUrl = "{{ route('trading/new') }}";
            var postUrlVerify = "{{ route('trading/verify') }}"
            var decodeHTML = function(html) {
                var txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value;
            };
            var descriptionHtml = '{!! $decodedhtml !!}';
            CreateListing();
            </script>
           
            @endpush
