@php
	use Illuminate\Support\HtmlString;
	use Illuminate\View\ComponentAttributeBag;

	/**
	 * @var ComponentAttributeBag $attributes
	 * @var HtmlString            $slot
	 * @var string                $name
	 * @var array                 $list
	 * @var string                $title
	 */

	$selectedValues = surfGetSelectedCheckboxValues($name);

@endphp
<fieldset data-name="{{$name}}">
	@if(isset($title))
		<legend>{{ $title }}</legend>
	@endif
	@foreach($list as $key => $value)
		<div class="item">
			<input value="{{$key}}" type="checkbox" id="{{$name.'-'.$key}}" name="{{$name}}"
			       {{ checked(in_array($key, $selectedValues)) }} class="hidden"/>
			<label for="{{$name.'-'.$key}}">{{$value}}</label>
		</div>
	@endforeach
</fieldset>
