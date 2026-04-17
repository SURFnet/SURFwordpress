<ul class="sub-menu">
	@foreach($children as $child)
		<li @class(['page-top-menu__item', ...$child['classes']])>
			<a href="{{ $child['url'] }}" target="{{ $child['target'] }}">
				{{ $child['title'] }}
			</a>

			@if(!empty($child['children']))
				<x-pages.top-menu-sub-toggle/>
				<x-pages.top-menu-sub :children="$child['children']"/>
			@endif
		</li>
	@endforeach
</ul>
