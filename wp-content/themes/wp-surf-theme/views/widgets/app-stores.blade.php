@php
	use SURF\Enums\Theme;

	/**
	 * @var string $title
	 * @var array $stores
	 */

	$mainColumnWidth = (Theme::isSURF() ? 'span-4-lg' : 'span-3-lg');
@endphp

@unless(empty($stores))
	<div class="column span-4-sm span-4-md {{ $mainColumnWidth }}">
		<div class="footer__menu-title h5"> {{ $title }}</div>
		@foreach($stores as $key => $store)
			<a href="{{ $store['url'] }}">
				<x-icon icon="app-{{ $key }}" sprite="global"
				        class="footer__app-store-icon footer__app-store-icon--{{ $key }}"/>
			</a>
		@endforeach
	</div>
@endunless
