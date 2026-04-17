@php
	use SURF\Core\PostTypes\BasePost;
	use SURF\Enums\Theme;
	use SURF\PostTypes\Agenda;
	use SURF\PostTypes\Asset;
	use SURF\PostTypes\Attachment;
	use SURF\PostTypes\Download;
	use SURF\PostTypes\Faq;
	use SURF\PostTypes\Page;
	use SURF\PostTypes\Post;
	use SURF\PostTypes\Vacancy;

	/**
	 * @var BasePost $post
	 */

	$postTypes = [
		Attachment::getName() => __('File', 'wp-surf-theme'),
		Post::getName() => __('Article', 'wp-surf-theme'),
		Page::getName() => __('Page', 'wp-surf-theme'),
		Agenda::getName() => __('Event', 'wp-surf-theme'),
		Asset::getName() => __('Asset', 'wp-surf-theme'),
		Download::getName() => __('File', 'wp-surf-theme'),
		Faq::getName() => __('FAQ', 'wp-surf-theme'),
		Vacancy::getName() => __('Vacancy', 'wp-surf-theme')
	];
	if (!array_key_exists($post['post_type'], $postTypes)) {
		return;
	}

@endphp
<div class="search-item">
	<h2 class="search-item__title search-item__title--{{ $post['post_type'] }} h3">
		<a href="{{ $post->permalink() }}" rel="bookmark">
			{!! surfGetHeadingIcon('h3') !!}
			{!! $post->title() !!}
		</a>
	</h2>
	<div class="search-item__type search-item__type--{{ $post['post_type'] }}">
		{{ $postTypes[$post['post_type']] }}
	</div>
	<p>{!! surfGetMyExcerpt(15, $post->ID(), '...') !!}</p>
	@if(Theme::isPoweredBy())
		<a href="{{ $post->permalink() }}" class="post-item__read-more">{{ __('Read more', 'wp-surf-theme') }}</a>
	@endif
</div>
