@php
	/**
	 * @var string $postType
	 * @var string $taxonomy
	 */

	$postType = $postType ?? null;
	$taxonomy = $taxonomy ?? null;

	$showExportButton = get_option('options_faq_archive_show_export_button', false);

	if (empty($postType) || !$showExportButton) {
		return;
	}
@endphp

<div class="archive__filter-item archive__export-button-wrapper">
	<button class="archive__filter-item-title" type="button">{{ __('Export', 'wp-surf-theme') }}
		<x-icon icon="chevron-down" sprite="global"/>
	</button>

	<div class="archive__filter-item-list">
		<div class="top-border-left"></div>
		<div class="top-border-right"></div>

		<button
				class="archive__filter-item-title archive__export-button"
				type="button"
				data-output="xlsx"
				data-post-type="{{ $postType }}"
				@if(!empty($taxonomy)) data-taxonomy="{{ $taxonomy }}" @endif
		>{{ __('Excel', 'wp-surf-theme') }}</button>
		<button
				class="archive__filter-item-title archive__export-button"
				id="export-button"
				type="button"
				data-output="pdf"
				data-post-type="{{ $postType }}"
				@if(!empty($taxonomy)) data-taxonomy="{{ $taxonomy }}" @endif
		>{{ __('PDF', 'wp-surf-theme') }}</button>
	</div>
</div>
