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
                <?php
                   $catCount = 0;
                ?>
                @foreach($cats->SuggestedCategoryArray->SuggestedCategory as $category)
                    <option {{ $catCount == 0  ? "selected" : "" }} value="{{ $category->Category->CategoryID }}">{{ $category->Category->CategoryName }}</option>
                <?php
                    $catCount++;
                ?>
                @endforeach
        @endisset
@endif
</select>
