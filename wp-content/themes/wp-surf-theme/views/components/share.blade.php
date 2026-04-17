@php
	use SURF\Helpers\SocialHelper;

	$post_id = get_the_ID();
	$shares  = SocialHelper::getShareList( $post_id );
	if ( empty( $shares ) ) {
		return;
	}

	if ( empty( $title ) ) {
		$title = __('Share this article', 'wp-surf-theme');
	}

@endphp
<div class="share">
	@if( empty( $clear ) )
		<div class="share__left">
			<span></span>
			<span></span>
		</div>
		<div class="share__line"></div>
	@endif
	<div class="share__title">{{ $title }} </div>
	<div class="share__link button-group button-group--line">
		@foreach( $shares as $share )
			<a href="{{ $share['url'] }}" {{ !empty($share['onclick']) ? 'onclick="' . $share['onclick'] . '"' : '' }}
			target="_blank" class="button-group__item" aria-label="{{ esc_attr($share['label']) }}">
				<x-icon :icon="$share['icon']" :sprite="$share['sprite']" class="share-icon"/>
			</a>
		@endforeach
	</div>
</div>
