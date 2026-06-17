@php
	use Illuminate\Http\Request;

	/**
	 * @var Request $request
	 */
@endphp

@extends('layouts.app')

@section('content')
	{{--  Custom component example  --}}
	<x-dev-notice title="Custom Route Example">
		This is an example of a custom route. You should probably delete this route!
	</x-dev-notice>

	{{--  Caching usage example: @cache('CACHE_KEY', TTL_IN_SECONDS)   --}}
	@cache('current_time_example', 10)
	{{ wp_date('Y-m-d H:i:s') }}
	@endcache
@endsection

