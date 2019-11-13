<?php
$options = null;
$Label = $label;
$ResponseName = $responseName;
$ClassName = $className;
$IdName = $idName;
$Name = $name;
$ResponseObj = $responseObj;
?>

@isset($ResponseObj->$ClassName)
<label for="{{$ResponseName}}">{{$Label}}</label>
    <select id="{{$ResponseName}}" name="{{$ResponseName}}" class="form-control">
        <option selected value="">Choose...</option>
        @foreach($ResponseObj->$ClassName as $class)
        <option value="{{ $class->$IdName }}">{{ $class->$Name }}</option>
        @endforeach
</select>
@endisset