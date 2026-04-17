@php
	use SURF\PostTypes\Attachment;

	/**
	 * @var Attachment $file
	 * @var string $description
	 */

@endphp
<div class="assets-file">
	<div class="assets-file__inner">
		<div class="assets-file__content">
			<h3 class="h5 assets-file__title">
				<x-icon icon="document" sprite="global"/>
				{!! surfGetHeadingIcon('h5') !!}
				{{ $file->title() }}
			</h3>
			<p>{{ $description }}</p>
		</div>
		<div class="assets-file__actions">
			<a class="button" id="page-download" href="{{ $file->url() }}" download>
				{{ __('Download', 'wp-surf-theme') }}
			</a>
		</div>
	</div>
	<div class="assets-file__meta">
		<span>{{ $file->extension() }}</span>
		<span>{{ surfFileSize($file->size()) }}</span>
	</div>
</div>
