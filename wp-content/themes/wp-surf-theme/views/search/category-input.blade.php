@php
	use SURF\Helpers\Helper;

	$request  = Helper::getSanitizedRequest('term', []);
	$selected = is_array($request) && in_array($term->term_id, $request);
	$terms    = get_terms( ['taxonomy' => $taxonomy->name, 'hide_empty' => false, 'parent' => $term->term_id])
@endphp

<li>
	<input onchange="this.form.submit()" type="checkbox" name="term[]"
	       value="{{ $term->term_id }}"
	       id="{{ $term->term_id }}" @checked($selected)>
	<label for="{{ $term->term_id }}">
		<span>{{ $term->name }}</span>
	</label>

	@if($terms && !is_wp_error($terms))
		<ul>
			@foreach($terms as $child)
				@include('search.category-input', ['term' => $child, 'taxonomy' => $taxonomy])
			@endforeach
		</ul>
	@endif
</li>
