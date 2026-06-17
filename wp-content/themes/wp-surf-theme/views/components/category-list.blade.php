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

	$collapse_after = max( 0, (int) ( $collapseAfter ?? 0 ) );
	$collapsible    = !empty( $collapsible ) && $collapse_after > 0;

	$items = [];
	if ( !empty( $withParents ) ) {
		foreach ( $term_list as $sub_list ) {
			if ( !empty( $sub_list['parent'] ) ) {
				$parent_item = $sub_list['parent'];
				$items[]     = [
					'class' => $prefix . '-item__tags__item--category',
					'url'   => $parent_item['url'] ?? null,
					'title' => $parent_item['title'] ?? $parent_item,
				];
			}

			foreach ( $sub_list['children'] ?? [] as $item ) {
				$items[] = [
					'class' => $prefix . '-item__tags__item',
					'url'   => $item['url'] ?? null,
					'title' => $item['title'] ?? $item,
				];
			}
		}
	} else {
		foreach ( $term_list as $item ) {
			$items[] = [
				'class' => $prefix . '-item__tags__item',
				'url'   => $item['url'] ?? null,
				'title' => $item['title'] ?? $item,
			];
		}
	}

	$total_count   = count( $items );
	$hidden_count  = ( $collapsible && $total_count > $collapse_after ) ? $total_count - $collapse_after : 0;
	$visible_items = $hidden_count > 0 ? array_slice( $items, 0, $collapse_after ) : $items;
	$hidden_items  = $hidden_count > 0 ? array_slice( $items, $collapse_after ) : [];
	$more_text     = sprintf( _n( 'Show the category', 'Show all %d categories', $total_count, 'wp-surf-theme' ), $total_count );
	$less_text     = sprintf( _n( 'Show the first category', 'Show first %d categories', $collapse_after, 'wp-surf-theme' ), $collapse_after );
	$toggle_sr     = _n( 'Toggle category', 'Toggle categories', $total_count, 'wp-surf-theme' );

@endphp
<div class="{{ $prefix }}__tags {{ $prefix }}-item__tags">
	@foreach( $visible_items as $item )
		<span class="{{ $item['class'] }} {{ !empty( $item['url'] ) ? 'has-link' : '' }}">
			@if( !empty( $item['url'] ) )
				{!! Helper::buildLink( $item['url'], $item['title'], $link_args ) !!}
			@else
				{!! $item['title'] !!}
			@endif
		</span>
	@endforeach

	@if( !empty( $hidden_items ) )
		<details class="{{ $prefix }}-item__tags__more">
			<summary class="{{ $prefix }}-item__tags__toggle">
				<span class="sr-only">{{ $toggle_sr }}</span>
				<span class="{{ $prefix }}-item__tags__toggle-closed">
					<span aria-hidden="true">+</span>
					<span class="toggle__text">{{ $more_text }}</span>
				</span>
				<span class="{{ $prefix }}-item__tags__toggle-open">
					<span aria-hidden="true">-</span>
					<span class="toggle__text">{{ $less_text }}</span>
				</span>
			</summary>
			@foreach( $hidden_items as $item )
				<span class="{{ $item['class'] }} {{ !empty( $item['url'] ) ? 'has-link' : '' }}">
					@if( !empty( $item['url'] ) )
						{!! Helper::buildLink( $item['url'], $item['title'], $link_args ) !!}
					@else
						{!! $item['title'] !!}
					@endif
				</span>
			@endforeach
		</details>
	@endif
</div>
