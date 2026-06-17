@php
	use SURF\Enums\Theme;
	use SURF\PostTypes\Download;

	/**
	 * @var Download $download
	 */

@endphp

@extends('layouts.app')

@section('content')
	<article id="post-{{$download->ID()}}"
	         {!! $download->postClass('entry entry--single') !!} @if(Theme::isSURF() && $download->getPrimaryCategoryColor($download->ID()))style='--surf-color-category: {{ $download->getPrimaryCategoryColor($download->ID()) }};' @endif>
		<div class="entry__header container padded">
			<x-breadcrumb/>
			<h1 class="entry__title">
				{!! surfGetHeadingIcon('h1') !!}
				{!! $download->title() !!}
			</h1>
			<ul class="entry__meta">
				<li class="entry__meta-category">
					<x-icon icon="document" sprite="global"/>
					@if($termId = $download->getPrimaryCategoryId($download->ID()))
						@php $showClosingATag = true; @endphp
						<a href="{{ get_term_link($termId) }}">
							@endif
							{{ (!empty($download->getPrimaryCategoryName($download->ID())) ? $download->getPrimaryCategoryName($download->ID()) : __('File', 'wp-surf-theme')) }}
							@if($showClosingATag ?? false)
						</a>
					@endif
				</li>
				<li>
					{{ (!empty($download->date('j F Y')) ? $download->date('j F Y') : get_the_date('', $download->ID())) }}
				</li>
				<li>
					{{ $download->getMeta('location') }}
				</li>
			</ul>
			@if(!empty($download->getTags()) && Theme::tagsLocation() == 'top')
				<div class="entry__tags tags-list">
					@foreach($download->getTags() as $tag)
						<a href="{{ get_term_link($tag->term_id) }}">{{ $tag->name }}</a>
					@endforeach
				</div>
			@endif
			@if(has_post_thumbnail() && !$download->shouldHideFeaturedImage())
				<div class="entry__figure">{!! $download->postThumbnail('post-image-full') !!}</div>
			@endif
		</div>
		<div class="entry__inner padded container">
			{!! $download->content() !!}
			@if(!empty($download->file()?->url()))
				<div class="wp-block-file">
					<div>
						<a id="page-download" href="{{ $download->file()?->url() }}">
							{{ $download->file()?->name() }}
						</a>
						<div class="wp-block-file__size">
							{{ surfFileSize($download->file()?->size()) }}
						</div>
					</div>

					<a href="{{ $download->file()?->url() }}" class="wp-block-file__button" download>
						{{ __('Download', 'wp-surf-theme') }}
					</a>
				</div>
			@endif
			@if(!empty($download->getTags()) && Theme::tagsLocation() == 'bottom')
				<div class="entry__tags tags-list">
					@foreach($download->getTags() as $tag)
						<a href="{{ get_term_link($tag->term_id) }}">{{ $tag->name }}</a>
					@endforeach
				</div>
			@endif
			@include('parts.single-contact-persons')
		</div>
	</article><!-- #post-{{ $download->ID() }} ?> -->

	{!! $download->editPostLink(text: _x('Edit', 'admin', 'wp-surf-theme'), class: 'post-edit-link button') !!}

	<div class="container padded">
		@include('components.share')
	</div>
@endsection

@push('head')
	@if( $download->file() )
		<script type="application/ld+json">
			{!! json_encode([
				'@context' => 'https://schema.org',
				'@type' => 'DigitalDocument',
				'name' => $download->file()?->name(),
				'url' => $download->file()?->url(),
				'image' => $download->postThumbnail() ? wp_get_attachment_image_url($download->postThumbnailId(), 'large') : null
			]) !!}
		</script>
	@endif
@endpush
