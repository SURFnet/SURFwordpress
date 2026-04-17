@php
	use SURF\PostTypes\Asset;

	$post_id = get_the_ID();
	if (empty($post_id)) {
		return;
	}

	$asset = Asset::find($post_id);
	if (empty($asset)) {
		return;
	}

@endphp
@include('asset.blocks.related-items', compact('asset'))
