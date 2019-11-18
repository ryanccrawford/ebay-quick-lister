<label for="primaryCategory">Primary Category</label>
    <select id="primaryCategory" name="primaryCategory" class="form-control">
@if(old('primaryCategory'))
            <option selected value="{{old('primaryCategory')}}">{{ old('primaryCategory') }}</option>
        @isset($cats->SuggestedCategoryArray)
            @foreach($cats->SuggestedCategoryArray->SuggestedCategory as $category)
                <option value="{{ $category->Category->CategoryID }}">{{ $category->Category->CategoryName }}</option>
            @endforeach
        @endisset
@else    
        @isset($cats->SuggestedCategoryArray)
             <option selected value="">Choose...</option>
                @foreach($cats->SuggestedCategoryArray->SuggestedCategory as $category)
                    <option value="{{ $category->Category->CategoryID }}">{{ $category->Category->CategoryName }}</option>
                @endforeach
        @endisset
@endif
</select>