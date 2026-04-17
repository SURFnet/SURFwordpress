@php

	use SURF\PostTypes\Asset;

	$archive_url = get_post_type_archive_link( Asset::getName() );

@endphp
<form action="{{ $archive_url }}">
	<div class="archive-page__sort-form">
		<x-dropdown-filter placeholder="{{ __('Sort', 'wp-surf-theme') }}" name="sort-by" :list="Asset::listSortingOptions()"/>
		<button type="submit" class="sr-only sort-submit">
			<span class="sr-only">{{ esc_attr_x( 'Sort assets', 'submit button', 'wp-surf-theme' ) }}</span>
		</button>
	</div>
</form>
