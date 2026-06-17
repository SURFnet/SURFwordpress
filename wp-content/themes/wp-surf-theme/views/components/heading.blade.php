@php
	use SURF\Helpers\Helper;

	$attr = $attr ?? [];
	if ( !empty( $class ) ) {
		$attr['class'] = $class;
	}

	$attr_string = Helper::buildAttributes( $attr );
	$prefix      = !empty( $icon ) ? surfGetHeadingIcon( $icon ) : '';

@endphp
@switch($tag ?? '')
	@case('h1')
		<h1 {!! $attr_string !!}>
			{!! $prefix !!}
			{{ $slot }}
		</h1>
		@break

	@case('h2')
		<h2 {!! $attr_string !!}>
			{!! $prefix !!}
			{{ $slot }}
		</h2>
		@break

	@case('h3')
		<h3 {!! $attr_string !!}>
			{!! $prefix !!}
			{{ $slot }}
		</h3>
		@break

	@case('h4')
		<h4 {!! $attr_string !!}>
			{!! $prefix !!}
			{{ $slot }}
		</h4>
		@break

	@case('h5')
	@default
		<h5 {!! $attr_string !!}>
			{!! $prefix !!}
			{{ $slot }}
		</h5>
		@break

@endswitch
