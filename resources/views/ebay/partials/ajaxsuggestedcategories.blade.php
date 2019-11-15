@isset($serviceResponse->SuggestedCategoryArray)
<label for="primaryCategory">Primary Category</label>
    <select id="primaryCategory" name="primaryCategory" class="form-control">
        <option selected value="">Choose...</option>
        <?php

        ?>
        @foreach($serviceResponse->SuggestedCategoryArray as $SuggestedCategory)
        {{ dump($SuggestedCategory) }}
        <option value="{{ $SuggestedCategory->Category['CategoryID'] }}">{{ $SuggestedCategory->Category['CategoryName'] }}</option>
        @endforeach
</select>
@endisset
