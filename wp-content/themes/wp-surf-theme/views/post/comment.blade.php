@php
	/**
	 * This is a comment template, this overwrites the standard WordPress comment template.
	 * @var $comment
	 * @var $args
	 * @var $depth
	 */
@endphp

<li {{ comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) }} id="comment-{{ comment_ID() }}">
    <span id="div-comment-{{ comment_ID() }}" class="comment__body">
        @if($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback')
		    <span class="pingback-entry"><span class="pingback-heading">{{ __( 'Pingback:', 'wp-surf-theme' ) }}</span> {{ comment_author_link() }}</span>
	    @endif

	    {!! edit_comment_link( __( 'Edit', 'wp-surf-theme' ), '  ', '' ) !!}

        <span class="comment__avatar">
            @if( $args['avatar_size'] != 0)
		        {!! get_avatar( $comment, 96) !!}
	        @endif
        </span>
        <span class="comment__author">
            <span class="h4">{!! get_comment_author_link() !!}</span>
        </span><!-- .comment-author -->

        @if($comment->comment_approved == '0')
		    <em class="comment__awaiting-moderation"> {{ __( 'Your comment is awaiting moderation.', 'wp-surf-theme' ) }}</em>
		    <br>
	    @endif

        <span class="comment__details">
            <span class="comment__text">
                {!! comment_text() !!}
            </span><!-- .comment-text -->

            <span class="comment__meta commentmetadata">
                <a href="{{ htmlspecialchars( get_comment_link( $comment->comment_ID ) ) }}">
                    <x-icon icon="calendar" sprite="global"/>
                    {{ get_comment_date() }}
                    <span class="comment__meta-time">{{ get_comment_time('G:i') }}</span>
                </a>
            </span><!-- .comment-meta -->

            <span class="comment__reply">
                {!! comment_reply_link( array_merge( $args, [ 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ] ) ) !!}
            </span> <!-- .reply -->
        </span><!-- .comment-details -->
    </span>
</li>
