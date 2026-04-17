@php
	use SURF\Helpers\Helper;

	/**
	 * @var string $name
	 * @var array $list
	 */

@endphp
<fieldset data-name="{{ $name }}">
	<select name="{{ $name }}" id="">
		@foreach( $list as $key => $value )
			<option value="{{ $key }}" {{ selected( Helper::getSanitizedGet( $name ), $key ) }}>
				{{ $value }}
			</option>
		@endforeach
	</select>
</fieldset>
