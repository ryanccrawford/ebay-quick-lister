@extends('layouts.app')

@section('headscripts')

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
            line-height: 200px;
            text-align: center
        }

        .upload-image-drop-zone.drop {
            color: #222;
            border-color: #222;
        }
</style>
@endsection

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
        <div class="row">
            <div class="col">
                <div class="card mb-3 mt-3">

                    <div class="card-body">
                        <h4 class="card-title">eBay Item Title</h4>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Title</span>
                            </div>
                            <input type="text" id="ebaytitle" class="form-control" placeholder="Item Title" aria-label="ItemTitle" aria-describedby="ebaytitle">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col px-md-5">
                <div class="col ">
                    <div class="card mb-3">
                        <img id="item-image" src="" class="card-img-top mt-3 center" alt="..." style="display:none;">
                        <div class="card-body">
                            <h4 class="card-title">Item Details</h4>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">SKU</span>
                                </div>
                                <input type="text" name="sku" id="sku" class="form-control" placeholder="Item SKU" aria-label="sku" aria-describedby="sku">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">QTY</span>
                                </div>
                                <input type="number" name="qty" id="qty" min="1" max="9000" class="form-control" aria-label="qty" aria-describedby="qty" disabled>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="price">Price $</span>
                                </div>
                                <input type="number" name="price" min=".01" max="9999999.00" step=".01" class="form-control" aria-label="price" aria-describedby="price" disabled>
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
                            <label for="desc">Product Description</label>
                            <div class="input-group mb-3">
                                <textarea class="form-control" id="desc" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col px-md-5">
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="card-title">Image Uploader</h4>
                        <form data-action="{{ route('image.upload.post') }}" method="POST" enctype="multipart/form-data" id="upload-image-form">
                          @csrf
                            <div class="form-inline">
                                <div class="form-group">
                                    <input type="file" name="file" class="form-control"  id="upload-image">   
    
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
        </div>
        <div class="row">
            <div class="col mt-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Description Output</h4>
                        <textarea id="HTMLeditor1" name="HTMLeditor1">
                        <?php
                        $html_str = file_get_contents('policy.html');
                        echo htmlentities($html_str);
                        ?>
                        </textarea>
                        <script>
                            var htmleditor = CKEDITOR.replace('HTMLeditor1');
                        </script>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mt-3 mb-3">
                    <div class="card">

                    </div>
                </div>
            </div>
        </div>


@endsection


