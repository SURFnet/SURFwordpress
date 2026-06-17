@php
	/**
	 * Archive filtering — Figma 1948-3568 (mobile/stacked), 1777-7534 (desktop/horizontal)
	 *
	 * @var int $count
	 */

	use SURF\PostTypes\Asset;

	$archive_url = get_post_type_archive_link( Asset::getName() );
@endphp
<div class="archive__filtering">
	<div class="archive__filtering__count">
		<x-icon icon="document" sprite="global"/>
		{!! sprintf( _n( '%1$s asset', '%1$s assets', $count, 'wp-surf-theme'), '<span class="found_item_count">' . $count . '</span>') !!}
	</div>
	<div class="archive__filtering__sort">
		<form action="{{ $archive_url }}">
			<x-dropdown-filter placeholder="{{ __('Sort', 'wp-surf-theme') }}" name="sort-by" :list="Asset::listSortingOptions()"/>
			<button type="submit" class="sr-only sort-submit">
				<span class="sr-only">{{ esc_attr_x( 'Sort assets', 'submit button', 'wp-surf-theme' ) }}</span>
			</button>
		</form>
	</div>
	<form action="{{ $archive_url }}" class="archive__filtering__search-form">
		<fieldset data-name="search">
			<label for="archive-asset-search" class="sr-only">{{ __('Search', 'wp-surf-theme') }}</label>
			<input id="archive-asset-search" type="text" name="search" value="{{ \SURF\Helpers\Helper::getSanitizedRequest('search', '') }}" placeholder="{{ __('Search in the assets', 'wp-surf-theme') }}"/>
		</fieldset>
		<button type="submit" class="archive__filtering__search-submit">
			<span class="sr-only">{{ esc_attr_x( 'Search', 'submit button', 'wp-surf-theme' ) }}</span>
			<x-icon icon="search" sprite="global"/>
		</button>
	</form>
</div>
