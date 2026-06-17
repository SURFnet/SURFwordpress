@php
	use SURF\Helpers\SocialHelper;

	// Only get the links if it's not passed
	if ( !isset( $socialLinks ) ) {
		$socialLinks = SocialHelper::getFollowList();
	}

	// Don't render the component if there are no links to show
	if ( empty( $socialLinks ) ) {
		return;
	}

	// Default title if not passed
	if ( !isset( $title ) ) {
		$title = __('Follow us', 'wp-surf-theme');
	}

@endphp
<h3 class="h5 {{ $headingClass ?? '' }}">{{ $title }}</h3>
<div class="button-group">
	@foreach ( $socialLinks as $social => $link )
		@if( !empty( $link['url'] ) )
			<a href="{{ $link['url'] }}" class="button-group__item" target="_blank"
			   aria-label="{{ esc_attr( $link['label'] ) }}">
				<x-icon :icon="$link['icon']" :sprite="$link['sprite']"/>
			</a>
		@endif
	@endforeach
</div>