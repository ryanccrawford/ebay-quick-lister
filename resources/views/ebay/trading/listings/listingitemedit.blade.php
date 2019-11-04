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
                 @isset($Errors)
            <div class="alert alert-danger Errors" role="alert">
                <strong>{{ $Errors['message'] }}</strong>
            </div>
            @endisset
                @isset($item)
                <h1 class="text-white">Editing Item {{$item->ItemID}}</h1>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3 mt-3">
                            <div class="card-body">
                                <h4 class="card-title">eBay Item Title</h4>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Title</span>
                                    </div>
                                <input maxlength="80" type="text" id="ebaytitle" class="form-control" placeholder="Item Title" aria-label="ItemTitle" aria-describedby="ebaytitle" value="{{$item->Title}}">
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="col-md-6">

                            <div class="card mb-3">
                                <img id="item-image" src="" class="card-img-top mt-3 center" alt="..." style="display:none;">
                                <div class="card-body">
                                    <h4 class="card-title">Item Details</h4>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">SKU</span>
                                        </div>
                                    <input maxlength="50" type="text" name="sku" id="sku" class="form-control" placeholder="Item SKU" aria-label="sku" aria-describedby="sku" value="{{$item->SKU}}">
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">QTY</span>
                                        </div>
                                    <input type="number" name="qty" id="qty" min="1" max="9000" class="form-control" aria-label="qty" aria-describedby="qty" value="{{$item->Quantity - $item->SellingStatus->QuantitySold }}">
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="price">Price $</span>
                                        </div>
                                    <input type="number" name="price" min=".01" max="9999999.00" step=".01" class="form-control" aria-label="price" aria-describedby="price" value="{{$item->SellingStatus->CurrentPrice->value}}">
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="shippingprice">Shipping Cost $</span>
                                        </div>
                                        <input type="number" name="shippingprice" min=".01" max="9999999.00" step=".01" class="form-control" aria-label="shippingprice" aria-describedby="shippingprice" disabled>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="shippingMethod">Shipping Method</label>
                                        <select id="shippingMethod" class="form-control" disabled>
                                            <option selected>Choose...</option>
                                            <option value="1">Buyer Pays (Calculated)</option>
                                            <option value="2">Free Shipping (Added to the Price)</option>
                                            <option value="3">Freight Shipping</option>
                                            <option value="4">Other</option>

                                        </select>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="col-md-6 px-md-5">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Image Uploader</h4>
                                <form data-action="{{ route('imagepost') }}" method="POST" enctype="multipart/form-data" id="upload-image-form">
                                    @csrf
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <input type="file" name="file" class="form-control" id="upload-image">

                                        </div>
                                        <button type="button" name="submit" class="btn btn-sm btn-primary" id="upload-image-submit">Upload</button>
                                    </div>
                                </form>

                                <!-- Drop Zone -->
                                <h4>Or Drag an Image Here</h4>
                                <div class="upload-image-drop-zone" id="upload-image-drop-zone">
                                    Just drag and drop files here
                                </div>

                                <!-- Progress Bar -->
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="upload-progress-bar">
                                        <span class="sr-only" id="text-complete"></span>
                                    </div>
                                </div>

                                <!-- Upload Finished -->
                                <div class="container">
                                    @if ($message = Session::get('success'))
                                    <div class="alert alert-success alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>{{ $message }}</strong>
                                    </div>
                                    <img src="images/{{ Session::get('image') }}">
                                    @endif
                                    @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>



                            </div>
                        </div>
                    </div>

                    </div>
                <div class="row justify-content-center">
                    @php
                      $count = count($item->PictureDetails->PictureURL);
                      $size = 4 / $count + 1;
                    @endphp
                <div class="col-md-{{intval($size)}}">
                         <div class="card">
                             <div class="button-group">
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true"><i class="fa fa-trash" aria-hidden="true"></i></span>
                             </button>
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true"><i class="fa fa-plus" aria-hidden="true"></i></span>
                             </button>
                             </div>
                            <img class="card-img" src="../images/placeholder-400x400.png" alt="..." style="border: 2px dashed black">

                    </div>
                </div>
                    @foreach($item->PictureDetails->PictureURL as $PictureDetails)
                <div class="col-md-{{intval($size)}}">
                         <div class="card">
                             <div class="button-group">
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close" name="del_image_">
                                    <span aria-hidden="true"><i class="fa fa-trash" aria-hidden="true"></i></span>
                             </button>
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true"><i class="fa fa-plus" aria-hidden="true"></i></span>
                             </button>
                             </div>
                            <img class="card-img" src="{{$PictureDetails}}" alt="..." style="border: 2px dashed black">

                        </div>
                     </div>
                     @endforeach
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Description Output</h4>
                                <textarea id="HTMLeditor1" name="HTMLeditor1">
                        <?php
                        echo htmlentities($item->Description);
                        ?>
                        </textarea>
                                <script>
                                    var htmleditor = CKEDITOR.replace('HTMLeditor1', {'height':'600'});
                                </script>
                            </div>
                        </div>
                </div>
                </div>
                <div class="row ">


                        <div class="card mt-5">
                            <div class="card-body">
                                <h4 class="card-title">Submit Item</h4>
                                <div id="alertarea">

                                </div>
                                <div id="alertarea2" style="display:none;">

                                </div>
                                <div id="alertarea3" style="display:none;">

                                </div>
                                <button id="preview" class="btn bg-primary text-white right" disabled>Preview</button>
                                <button id="savetoebay" class="btn bg-primary text-white right" disabled>Save Item</button>
                                <button id="copyhtml" class="btn bg-secondary text-white right">Copy HTML</button>
                            </div>
                        </div>
                    </div>
                    @endisset
                </div>



                @endsection
                @push('end')
                <script src="../js/createinventory.js"></script>

                @endpush

