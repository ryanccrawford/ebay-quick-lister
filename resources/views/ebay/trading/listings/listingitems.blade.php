@extends('layouts.app')

@section('content')
<div class="container">
<nav aria-label="Page navigation example">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" href="{{$prev_link }}">Previous</a></li>
   
    <li class="page-item"><a class="page-link" href="{{  $next_link }}">Next</a></li>
  </ul>
</nav>
    <div class="row justify-content-center">
        <div class="col-md-12">
      
            @if (count($errors->messages) > 0)
            <div class="alert alert-danger" role="alert">
                <strong>{{ $errors->messages }}</strong>
            </div>
            @endif
           
            <div class="container">
            <span><h1 class="mt-1 mb-2">Listings<a class="btn btn-danger round ml-2" href="#">{{ __('Add') }}</a></h1></span>
              
                

                <div class="col-lg-12">  
                    <div class="row justify-content-center card-group">
                @isset($listingItems)
                     @foreach ($listingItems as $listingItem)
                    <div class="col-lg-4">
                        <div class="card shadow rounded">
                        <div class="row no-gutters">    
                        <div class="col-md-3">
                           <img src="{{ $listingItem->PictureDetails->GalleryURL }} " class="card-img-top" alt="photo">
                        </div>
                           <div class="col-md-7">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $listingItem->Title }}</h5>
                                        <h5 class="card-text">SKU: {{ $listingItem->SKU }}</h5>
                                        <p class="card-text">$ {{  $listingItem->SellingStatus->CurrentPrice->value}}<br></p>
                                        <p class="card-text">
                                            {{ $listingItem->Description }}
                                           
                                        </p>
                                    </div>
                                </div>  
                            <div class="col-md-1">
                            <div class="card-body">
                                <a href="#" class="btn btn-primary btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>
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
@push('end')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
@endpush
