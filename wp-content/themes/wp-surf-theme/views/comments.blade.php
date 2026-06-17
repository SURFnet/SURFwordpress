<div class="comments">
	<header class="comments__header">
		<h2 class="comments__header-title">
			{!! surfGetHeadingIcon('h2') !!}
			{{ __('Reactions', 'wp-surf-theme') }}
		</h2>
		<p class="surf-block-paragraph is-style-lead">{{ __('Do you have something to say or you want to share something about this message, please feel free to start a discussion!', 'wp-surf-theme') }}</p>
	</header>
	@if(have_comments())
		<ul class="comments__list">
			{{ wp_list_comments([
				'style' => '',
				'callback' => function($comment, $args, $depth) {
					echo surfView('post.comment', compact('comment', 'args', 'depth'));
				}
			]) }}
		</ul>
	@else
		<div class="comments__none">
			<p>{{ __("No reactions yet, are you the first one?", 'wp-surf-theme') }}</p>
		</div>
	@endif
	<div class="comments__form">
		{{ comment_form() }}
	</div>
</div>
