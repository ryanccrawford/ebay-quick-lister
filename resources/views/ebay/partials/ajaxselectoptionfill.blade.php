<?php
$options = null;
$Label = $label;
$ResponseName = $responseName;
$ClassName = $className;
$IdName = $idName;
$Name = $name;
$ResponseObj = $responseObj;
$classList = lcfirst($ClassName);
?>

@isset($ResponseObj->$classList)
<label for="{{$ResponseName}}">{{$Label}}</label>
    <select id="{{$ResponseName}}" name="{{$ResponseName}}" class="form-control">
        <?php
            $otherCount = array ( $ResponseName => 0);
        ?>
        @foreach($ResponseObj->$classList as $class)
        <option {{ $otherCount[$ResponseName] === 0 ? "selected" : "" }} value="{{ $class->$IdName }}">{{ $class->$Name }}</option>
        <?php
            $otherCount[$ResponseName]++
        ?>
        @endforeach

</select>
@endisset
