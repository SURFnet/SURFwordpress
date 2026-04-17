@php
	/**
	 * @var array|null $filters
	 * @var string|null $orderBy
	 * @var string|null $order
	 */

	$filters = $filters ?? [];
	$orderBy = $orderBy ?? null;
	$order = $order ?? null;
@endphp

<table>
	<thead>
	<tr>
		<th>{{ __('Filter', 'wp-surf-theme') }}</th>
		<th>{{ __('Value', 'wp-surf-theme') }}</th>
	</tr>
	</thead>
	<tbody>
	@unless(empty($orderBy))
		<tr>
			<td>{{ __('Order by', 'wp-surf-theme') }}</td>
			<td>{{ ucfirst($orderBy) }}</td>
		</tr>
	@endunless
	@unless(empty($order))
		<tr>
			<td>{{ __('Order', 'wp-surf-theme') }}</td>
			<td>{{ strtolower($order) === 'desc' ? __('Descending', 'wp-surf-theme'): __('Ascending', 'wp-surf-theme') }}</td>
		</tr>
	@endunless
	@unless(empty($filters))
		@foreach($filters as $filter)
			<tr>
				<td>{{ $filter['parent'] ?? __('Category', 'wp-surf-theme') }}</td>
				<td>{{ $filter['name'] }}</td>
			</tr>
		@endforeach
	@endunless
	</tbody>
</table>
