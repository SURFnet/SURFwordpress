<form role="search" method="get" class="search-form" action="{{ home_url( '/' ) }}">
	<label>
		<span class="screen-reader-text">{{ _x( 'Search for:', 'label', 'wp-surf-theme' ) }}</span>
		<input type="search" class="search-field"
		       placeholder="{{ esc_attr_x( 'Search', 'placeholder', 'wp-surf-theme' ) }}"
		       {{-- get_search_query is already safe and escaped --}}
		       value="{{ is_search() ? get_search_query() : '' }}" name="s"
		       title="{{ esc_attr_x( 'Search for:', 'label', 'wp-surf-theme') }}"/>
	</label>
	<button type="submit" class="search-submit">
		<span class="sr-only">{{ esc_attr_x( 'Search', 'submit button', 'wp-surf-theme' ) }}</span>
		<x-icon icon="search" sprite="global" class="search-form__icon"/>
	</button>
</form>
