@php
	use SURF\PostTypes\Page;

	/**
	 * @var Page $page
	 */
@endphp

<article id="post-{{ $page->ID() }}" {!! $page->postClass('entry entry--page') !!}>
	<div class="entry__inner padded container {{ ($page->fullWidthTitle() ? 'title-is-style-extra-wide' : '') }}">
		<x-pages.top-menu :page="$page" location="top"/>

		@unless($page->hideBreadcrumbs())
			<x-breadcrumb/>
		@endunless

		@if(!is_front_page() && !$page->hideTitle())
			@if($page->showDate())
				<ul class="entry__meta">
					<li>
						<x-post-date postId="{{ $page->ID() }}"/>
					</li>
				</ul>
			@endif

			<h1 class="entry__title">
				{!! surfGetHeadingIcon('h1') !!}
				{!! $page->title() !!}
			</h1>
		@endif

		@if($page->getTopMenuLocation() === 'left')
			<div class="entry__sidebar is-style-extra-wide">
				<div class="entry__sidebar-menu">
					<x-pages.top-menu :page="$page" location="left"/>
				</div>
				<div class="entry__sidebar-content">
					{!! $page->content() !!}
				</div>
			</div>
		@else
			{!! $page->content() !!}
		@endif
		@include('parts.single-contact-persons')
	</div>
</article><!-- #post-{{ $page->ID() }} -->

{!! $page->editPostLink(text: _x('Edit', 'admin', 'wp-surf-theme'), class: 'post-edit-link button') !!}
