@isset($categories)
<label for="primaryCategory">Primary Category</label>
    <select id="primaryCategory" name="primaryCategory" class="form-control">
        <option selected value="">Choose...</option>
        @foreach($categories as $category)
   
        <option value="{{ $category->CategoryID }}">{{ $category->CategoryName }}</option>
        @endforeach
    
</select>
@endisset
