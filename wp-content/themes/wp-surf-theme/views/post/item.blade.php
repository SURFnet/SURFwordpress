@php
	use SURF\Core\PostTypes\BasePost;
	use SURF\Enums\Theme;
	use SURF\PostTypes\Vacancy;

	/**
	 * Post item.
	 * @var $type
	 * Type: row: Shows image on desktop on the left side, content on the right.
	 * Type: block: Shows image on top, content on the bottom.
	 * type: large: Shows up large
	 * @var BasePost $post
	 * @var boolean $hideImagesOnMobile
	 * @var string $label
	 * @var string $headingTag
	 */

	$postID = $post->ID();
	$label = $label ?? __('Read article', 'wp-surf-theme');
	$type = $type ?? 'block';
	$headingTag = $headingTag ?? 'h3';
	$headingClass = 'post-item__title ' . ( !empty( $headingSize[$type] ) ? $headingSize[$type] : ' h3' );
	$headingSize['large'] = 'h3';
	$headingSize['row'] = 'h5';
	$dateDisplay = $dateDisplay ?? false;

	$categoryName = $post->getCategoryName(true);
	$categoryColor = $post->getCategoryColor(true);
	$hasCategory = !empty($categoryName);
	$showDate = get_post_type($postID) !== Vacancy::getName() && Theme::postDateDisplay($postID) !== 'hidden';

	if ($dateDisplay) {
		$showDate = $dateDisplay !== 'hidden';
	}

	$hideCategories = $hideCategories ?? false;
	$hideImagesOnMobile = $hideImagesOnMobile ?? false;
	$hasPostThumbnail = has_post_thumbnail($postID);
@endphp

<article
		id="post-{{ $postID }}"
		{!! $post->postClass('post-item post-item--' . $type . ($hideImagesOnMobile ? ' post-item--hide-images-mobile' : '')) !!}
		@if( Theme::isSURF() && !empty($categoryColor) )style='--surf-color-category: {{ $categoryColor }};'@endif
>
	<div class="post-item__inner">
		<div class="post-item__content {{ !$hasPostThumbnail ? 'post-item__content--no-image' : '' }}">
			@if( !$hasPostThumbnail && !$hideCategories && $hasCategory )
				<div class="post-item__inline-category">
					<x-badge>{{ $categoryName }}</x-badge>
					<x-icon class="post-item__arrow-icon" :icon="'arrow-right'" :sprite="'global'"/>
				</div>
			@endif

			<x-heading
					:tag="$headingTag"
					:class="$headingClass"
			>
				<a href="{{ $post->permalink() }}" rel="bookmark">
					{!! $post->title() !!}
					@if( !$hasCategory )
						<x-icon class="post-item__arrow-icon" :icon="'arrow-right'" :sprite="'global'"/>
					@endif
				</a>
			</x-heading>
			<p>{!! surfGetMyExcerpt($type === 'row' ? 10 : 30, $postID, '...') !!}</p>

			@if( $showDate )
				<dl class="meta-list">
					<div>
						<dt class="sr-only">{{ __('Updated on', 'wp-surf-theme') }}</dt>
						<dd>
							<x-icon :icon="'calendar'" :sprite="'global'"/>
							<x-post-date postId="{{ $postID }}" :overRide="$dateDisplay"/>
						</dd>
					</div>
				</dl>
			@endif

			@if( Theme::isPoweredBy() )
				<a href="{{ $post->permalink() }}" class="post-item__read-more">{{ $label }}</a>
			@endif

			@if( !$hideCategories && $hasCategory && $hasPostThumbnail && $hideImagesOnMobile && $type === 'block' )
				<div class="post-item__inline-category post-item__inline-category--visible-on-mobile">
					<x-badge>{{ $categoryName }}</x-badge>
					<x-icon class="post-item__arrow-icon" :icon="'arrow-right'" :sprite="'global'"/>
				</div>
			@endif
		</div>
		@if( $hasPostThumbnail )
			<div class="post-item__figure @if( $hideImagesOnMobile ) post-item__figure--hidden-on-mobile @endif">
				@if( !$hideCategories && $hasCategory )
					<x-badge>{{ $categoryName }}</x-badge>
				@endif

				<a href="{{ $post->permalink() }}" rel="bookmark">
					{!! $post->postThumbnail($type === 'large' ? 'post-image-large' : 'post-image') !!}
				</a>
			</div>
		@endif
	</div>
</article>
<!-- #post-{{ $postID }} ?> -->
