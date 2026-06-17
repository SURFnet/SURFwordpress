@php

	use SURF\PostTypes\Asset;

	$archive_url = get_post_type_archive_link( Asset::getName() );

@endphp
<form action="{{ $archive_url }}">
	<div class="archive-page__search-form">
		<x-search-filter placeholder="{{ __('Search in the assets', 'wp-surf-theme') }}"/>
		<button type="submit" class="search-submit">
			<span class="sr-only">{{ esc_attr_x( 'Search', 'submit button', 'wp-surf-theme' ) }}</span>
			<x-icon icon="search" sprite="global" class=""/>
		</button>
	</div>
</form>
