@php
	use SURF\Helpers\PolylangHelper;

	/**
	 * @var array $args
	 * @var array $instance
	 * @var array $settings
	 */

	$id = uniqid();
	$postTypes = get_post_types( ['exclude_from_search' => false], 'objects' );
	$postTypes = array_filter($postTypes, fn ($postType) =>
			$postType->name !== 'attachment' && wp_count_posts($postType->name)->publish > 0
		);

	$postTypes = array_map(function($postType) {
		return PolylangHelper::getThemeOption("search_name_$postType->name") ?: $postType->label;
	}, $postTypes);

@endphp
<div class="archive-filter__group" data-accordion-item>
	<div class="archive-filter__header desktop-accepted" aria-controls="accordion-{{ $id }}" aria-expanded="true"
	     aria-label="{{ __('Toggle post types', 'wp-surf-theme') }}" data-accordion-button>
		<span class="h4">{{ $settings['title'] ?? __('Post type', 'wp-surf-theme') }}</span>
		<div class="archive-filter__toggle desktop-accepted ">
			<x-icon icon="chevron-down" sprite="global"/>
		</div>
	</div>
	<div class="archive-filter__list   desktop-accepted " id="accordion-{{ $id }}" data-accordion-target>

		<div class="archive-filter__item item">
			<x-checkbox-filter name="post_type" :list="$postTypes"/>
		</div>
	</div>
</div>
