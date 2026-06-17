@php
	use SURF\Helpers\SearchHelper;

	/**
	 * @var array $blockAttributes
	 * @var string $content
	*/

	$contentVariation = $blockAttributes['contentVariation'];
	$headingTag = $blockAttributes['headingTag'] ?? 'h1';
	$title = $blockAttributes['title'] ?? '';
	$tagline = $blockAttributes['tagline'] ?? '';
	$searchEnabled = $blockAttributes['searchEnabled'] ?? false;
	$searchAlignment = $blockAttributes['searchAlignment'] ?? 'bottom';
	$searchWidth = $blockAttributes['searchWidth'] ?? 'small';
	$placeholder = $blockAttributes['placeholder'] ?? __('Search...', 'wp-surf-theme');
	$searchform = SearchHelper::getForm(placeholder: $placeholder);
	$minHeight = $blockAttributes['minHeight'] ?? 'none';
	$backgroundColor = $blockAttributes['backgroundColor'] ?? '#ffffff';
	$textColor = $blockAttributes['textColor'] ?? '#000000';
	$horizontalAlignment = $blockAttributes['horizontalAlignment'] ?? 'center';
	$verticalAlignment = $blockAttributes['verticalAlignment'] ?? 'middle';
	$mediaType = $blockAttributes['mediaType'] ?? 'image';
	$mediaLocation = $blockAttributes['mediaLocation'] ?? null;
	$videoURL = $blockAttributes['video'] ?? '';
	$roundedCornersEnabled = $blockAttributes['roundedCornersEnabled'] ?? true;
@endphp

<article class="surf-block surf-block-hero"
         style="background-color: {{ $backgroundColor }}; color: {{ $textColor }} !important;">
	<section class="surf-block-hero__backdrop-layer">
	</section>
	<section
			class="surf-block-hero__content-layer surf-block-hero__content-layer--{{$minHeight}} surf-block-hero__content-layer--{{ $contentVariation }} {{ ($contentVariation === 'content-with-media' && $mediaLocation === 'left') ? 'surf-block-hero__content-layer--content-with-media--media-left' : '' }}">
		<div
				class="surf-block-hero__content surf-block-hero__content--{{$verticalAlignment}} surf-block-hero__content--{{$horizontalAlignment}} surf-block-hero__content--search-{{$searchWidth}} ">
			@if($searchEnabled && $searchAlignment === 'top')
				{!! $searchform !!}
			@endif
			@if($headingTag && $title)
				<x-heading :tag="$headingTag" class="h1">
					{!! $title !!}
				</x-heading>
		@endif
		@if($tagline)
			<p>{!! $tagline !!}</p>
			@endif

			@if($content)
				{!! $content !!}
			@endif

			@if($searchEnabled && $searchAlignment === 'bottom')
				{!! $searchform !!}
			@endif
			</div>
			@if($contentVariation)
				<figure
						class="surf-block-hero__media {{ $minHeight !== 'none' ? 'surf-block-hero__media--fill' : '' }} {{ !$roundedCornersEnabled ? 'surf-block-hero__media--no-rounded-corners' : '' }}">
					@if($mediaType === 'image')
						@if($contentVariation === 'content-with-media')
							<img src="{{$blockAttributes['image']['sizes']['hero-small']['url']}}">
						@else
							<img src="{{$blockAttributes['image']['sizes']['hero-large']['url']}}">
						@endif
					@endif
					@if($mediaType === 'video')
						<video autoplay="autoplay" loop="loop" muted="muted" playsinline="" webkit-playsinline="">
							<source src="{{ $videoURL }}" type="video/mp4"/>
						</video>
					@endif
				</figure>
			@endif
	</section>
</article>
