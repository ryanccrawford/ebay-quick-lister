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
                        <h1 class="mt-1 mb-1">Inventory Add Location</h1>
                         <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">New Inventory Location</h5>
                                <form action="{{ route('inventory/savelocation') }}" method="POST" name="location">
                                      @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label for="name">Location Name</label>
                                        <input type="text" max="1000" class="form-control" name="name" id="name" placeholder="Location Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="locationTypes">Select all that apply</label>
                                                <select name="locationTypes" class="form-control" id="locationTypes" multiple>
                                                    <option value="C_STORE">Store</option>
                                                    <option value="C_WAREHOUSE">Warehouse</option>
                                                </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="addressLine1">Address Line 1</label>
                                        <input type="text" class="form-control" id="addressLine1" name="addressLine1" placeholder="1234 Main St">
                                    </div>
                                    <div class="form-group">
                                        <label for="addressLine2">Address Line 2</label>
                                        <input type="text" class="form-control" id="addressLine2" name="addressLine2" placeholder="Apartment, studio, or floor">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label for="city">City</label>
                                            <input type="text" class="form-control" name="city" id="city" placeholder="The City">
                                        </div>
                                        <div class="form-group col-md-4">
                                        <label for="state">State</label>
                                            <select id="state" name="state" class="form-control">
                                                <option selected>Choose...</option>
                                                <option>Florida</option>
                                                <option>Virgina</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                        <label for="postalCode">Zip Code</label>
                                            <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="01234">
                                        </div>
                                    </div>
                                    <button type="submit" id="submit" class="btn btn-primary">Create</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection
@push('start')
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<!-- <script>

$(document).ready(function(){


   $('#submit').on('click', (event) => {
       event.preventDefault();
        let url = "{{ route('inventory/savelocation') }}";
        let formdata = $('[name="location"]')[0];
        const axiosOptions = {
            method: 'POST',
            url: url,
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        };
        axios(axiosOptions)
            .then(response => {
                console.log(response.data)
            }
            ).catch(response => {
                console.log(response)
        })
   })

}
)
</script> -->
@endpush
