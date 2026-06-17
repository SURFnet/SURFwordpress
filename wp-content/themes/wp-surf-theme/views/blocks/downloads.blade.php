@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Core\Blocks\Block;
	use SURF\PostTypes\Download;

	/**
	 * @var array $blockAttributes
	 * @var string $content
	 * @var boolean $hideImagesOnMobile
	 * @var string $blockName
	 * @var PostCollection $downloads
	 * @var Block $block
	 */

@endphp

<section class="{{ $block->inEditor() ? 'pointer-events-none' : 'surf-block surf-block-downloads alignfull' }}">
	<div class="surf-block-downloads__inner outer-block">
		<div class="container padded">
			@if(!$block->inEditor())
				<div class="block-header">
					<h2 class="surf-block-downloads__title block-header__title">{!! $blockAttributes['title'] ?? '' !!}</h2>
					<p class="surf-block-downloads__intro block-header__intro">{!! $blockAttributes['intro'] ?? '' !!}</p>
				</div>
			@endif

			<div class="surf-block-downloads__items grid">
				@foreach($downloads as $download)
					<div class="column span-4-sm span-4-md span-4-lg">
						@include('download.item', ['download' => $download, 'type' => 'block', 'hideImagesOnMobile' => $hideImagesOnMobile])
					</div>
				@endforeach
			</div>

			@if(!$block->inEditor())
				<div class="surf-block-downloads__bottom block-footer">
					<a href="{{ Download::getArchiveLink() }}" class="button">
						{{ $blockAttributes['buttonText'] ?? __('View all files', 'wp-surf-theme') }}
					</a>
				</div>
			@endif
		</div>
	</div>
</section>
