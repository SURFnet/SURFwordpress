@php
	/**
	 * @var int $total
	 * @var int $current
	 * @var string $parameter
	 */

	$parameter = $parameter ?? '_page';

@endphp
@if( $total > 1 )
	<div class="pagination">
		<nav class="navigation pagination">
			<div class="nav-links">
				@if($current > 1)
					<a href="{{ add_query_arg($parameter, $current - 1) }}" class="prev page-numbers">
						{{ __('Previous', 'wp-surf-theme') }}
					</a>
				@endif


				@for($i = 1; $i <= $total + 1; $i++)
					@if($i === $current)
						<span class="page-numbers current" aria-current="page">{{ $i }}</span>
					@else
						<a class="page-numbers" href="{{ add_query_arg($parameter, $i) }}"
						   aria-current="page">{{ $i }}</a>
					@endif
				@endfor

				@if($current <= $total)
					<a href="{{ add_query_arg($parameter, $current + 1) }}" class="next page-numbers">
						{{ __('Next', 'wp-surf-theme') }}
					</a>
				@endif
			</div>
		</nav>
	</div>
@endif

