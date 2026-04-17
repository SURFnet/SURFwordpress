@php
	use SURF\Helpers\Helper;

	/**
	 * @var string $name
	 * @var string $class
	 * @var array $list
	 * @var mixed $default
	 */

	$default = $default ?? null;

@endphp
<fieldset data-name="{{ $name }}" class="{{ $class ?? '' }}">
	@foreach( $list as $key => $value )
		<div class="item">
			<input value="{{ $key }}" id="{{ $key }}" type="radio"
			       name="{{ $name }}" {{ checked( Helper::getSanitizedGet( $name, $default ), $key ) }}>
			<label for={{ $key }}>{{ $value }}</label>
		</div>
	@endforeach
</fieldset>
