@php
	global $wp_query;

@endphp
<div class="pagination">
	{!!
		the_posts_pagination(
			[
				'format'    => '?paged=%#%',
				'total'     => $wp_query->max_num_pages,
				'current'   => get_query_var('paged') ?: 1,
				'end_size'  => 1,
				'mid_size'  => 1,
			]
		)
	!!}
</div>
