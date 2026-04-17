@props([
    'postId' => null,
    'overRide' => false
])

@php
	use SURF\Enums\Theme;

	$display = Theme::postDateDisplay($postId);
	$publishedAtDate = get_the_date('', $postId);
	$publishedAt = sprintf('%s%s', Theme::postDateDisplayPublishedAtText(), $publishedAtDate);
	$modifiedAt = sprintf('%s%s', Theme::postDateDisplayModifiedAtText(), get_the_modified_date('', $postId));
@endphp

@if(in_array(get_post_type($postId), ['post', 'page']) || $overRide)
	@php
		$display = $overRide ?: $display;
	@endphp
	@switch($display)
		@case('modified')
			{!! $modifiedAt !!}
			@break
		@case('both')
			{!! $publishedAt !!}, {!! $modifiedAt !!}
			@break
		@case('hidden')
			@break
		@default
			{!! $publishedAt !!}
	@endswitch
@else
	{!! $publishedAtDate !!}
@endif
