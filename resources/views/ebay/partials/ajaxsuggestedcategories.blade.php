@isset($cats->SuggestedCategoryArray)
<?php
   // echo var_dump($cats->SuggestedCategoryArray->SuggestedCategory)
?>
<label for="primaryCategory">Primary Category</label>
    <select id="primaryCategory" name="primaryCategory" class="form-control">
        <option selected value="">Choose...</option>
        @foreach($cats->SuggestedCategoryArray->SuggestedCategory as $category)
        <option value="{{ $category->Category->CategoryID }}">{{ $category->Category->CategoryName }}</option>
        @endforeach

</select>
@endisset
