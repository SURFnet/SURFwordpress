@php
	use SURF\Helpers\Helper;
	use SURF\Core\Taxonomies\TermCollection;

	/**
	 * @var TermCollection $list
	 * @var string $prefix
 	 **/

	if ( $list->isEmpty() ) {
		return;
	}

	$has_archive = !empty( $hasArchive );
	$link_args   = ['class' => $prefix . '-item__link', 'rel' => 'tag'];
	$term_list   = !empty( $withParents ) ?
		surfFormatTermsWithParents( $list, $has_archive ) :
		surfFormatTerms( $list, $has_archive );

	$primary = $primaryName ?? null;
	if ( !empty( $primary ) ) {
		$first_item = $term_list[ $primary ] ?? null;

		// loop through the list with parent & children
		if ( $withParents && empty( $first_item ) ) {
			foreach ( $term_list as $key => $item ) {
				if ( empty( $item['children'] ) || !is_array( $item['children'] ) ) {
					continue;
				}

				if ( !empty( $item['children'][ $primary ] ) ) {
					$first_item = [
						'children' => [ $primary => $item['children'][ $primary ] ]
					];
					unset( $term_list[ $key ]['children'][ $primary ] );
					break;
				}
			}
		}

		if ( !empty( $first_item ) ) {
			unset( $term_list[ $primary ] );
			$term_list = array_merge([$primary => $first_item], $term_list);
		}
	}

@endphp
<div class="{{ $prefix }}__tags {{ $prefix }}-item__tags">
	@if( !empty( $withParents ) )
		@foreach( $term_list as $sub_list )
			@if( !empty( $sub_list['parent'] ) )
				<span class="{{ $prefix }}-item__tags__item--category {{ !empty( $sub_list['parent']['url'] ) ? 'has-link' : '' }}">
					@if( !empty( $sub_list['parent']['url'] ) )
						{!! Helper::buildLink( $sub_list['parent']['url'], $sub_list['parent']['title'], $link_args ) !!}
					@else
						{!! $sub_list['parent'] !!}
					@endif
				</span>
			@endif
			@foreach( $sub_list['children'] ?? [] as $item )
				<span class="{{ $prefix }}-item__tags__item {{ !empty( $item['url'] ) ? 'has-link' : '' }}">
					@if( !empty( $item['url'] ) )
						{!! Helper::buildLink( $item['url'], $item['title'], $link_args ) !!}
					@else
						{!! $item !!}
					@endif
				</span>
			@endforeach
		@endforeach
	@else
		@foreach( $term_list as $item )
			<span class="{{ $prefix }}-item__tags__item {{ !empty( $item['url'] ) ? 'has-link' : '' }}">
			@if( !empty( $item['url'] ) )
					{!! Helper::buildLink( $item['url'], $item['title'], $link_args ) !!}
				@else
					{!! $item !!}
				@endif
			</span>
		@endforeach
	@endif
</div>
