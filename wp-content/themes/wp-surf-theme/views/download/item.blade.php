@php
	use SURF\Enums\Theme;
	use SURF\PostTypes\Download;

	/**
	 * @var Download $download
	 * @var $type
	 * Type: row: Shows image on desktop at the left side, content on the right.
	 * Type: block: Shows image on top, content on the bottom.
	 * type: large: Shows up large
	 */

	$headingTag = $headingTag ?? 'h3';

@endphp
<article
		{!! $download->postClass('post-item post-item--'.$type).' post-item--file'!!}  @if(Theme::isSURF() && $download->getPrimaryCategoryColor($download->ID()))style='--surf-color-category: {{ $download->getPrimaryCategoryColor($download->ID()) }};' @endif>
	<div class="post-item__inner">
		<div class="post-item__content">
			<x-heading :tag="$headingTag" class="post-item__title post-item__title--has-icon h4">
				<a href="{!! $download->permalink() !!}" class="no-arrow">
					<x-icon icon="document" sprite="global"/>
					{!! $download->title() !!}
				</a>
			</x-heading>
			@if(!empty($download->getPrimaryCategoryName($download->ID())))
				<ul class="post-item__meta">
					<li class='post-item__download-category'>
						<x-icon icon="tag" sprite="global"/>
						{{ $download->getPrimaryCategoryName($download->ID())}}
					</li>
				</ul>
			@endif
			<p>{!! surfGetMyExcerpt(15, $download->ID(), '...') !!}</p>
			@if($download->file())
				<div class="post-item__download">
					<a download href="{{ $download->file()->url() }}"
					   class="button--secondary button--download">
						{{ __('Download', 'wp-surf-theme') }}
					</a>
					<span>
                        {{ $download->file()?->extension() }} — {{surfFileSize($download->file()?->size())}}
                    </span>
				</div>
			@endif
		</div>
		@if(has_post_thumbnail($download->ID()))
			<div class="post-item__figure @if($hideImagesOnMobile) post-item__figure--hidden-on-mobile @endif">
				<a href="{!! $download->permalink() !!}">
					{!! $download->postThumbnail('post-image') !!}
				</a>
			</div>
		@endif
	</div>
</article>
