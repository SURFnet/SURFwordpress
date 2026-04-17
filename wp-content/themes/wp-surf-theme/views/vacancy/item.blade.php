@php
	use SURF\Enums\Theme;
	use SURF\PostTypes\Vacancy;

	/**
	 * @var $type
	 * Type: row: Shows image on desktop on the left side, content on the right.
	 * Type: block: Shows image on top, content on the bottom.
	 * type: large: Shows up large
	 * @var Vacancy $vacancy
	 */

	$headingTag = $headingTag ?? 'h3';
	$vacancyID  = $vacancy->ID();
	$color      = $vacancy->getPrimaryCategoryColor($vacancyID);
	$category   = $vacancy->getPrimaryCategoryName($vacancyID);

@endphp
<div {!! $vacancy->postClass('post-item post-item--'.$type) !!} @if(Theme::isSURF() && $color)style='--surf-color-category: {{ $color }};'@endif>
	<div class="post-item__inner">
		@if(!has_post_thumbnail($vacancyID))
			<div class="post-item__content post-item__content--no-image">
				@if(!empty($vacancy->primaryCategory()?->name))
					<div class="post-item__category">{{ $vacancy->getCategoryName() }}</div>
				@endif
		@else
			<div class="post-item__content">
		@endif
				<x-heading :tag="$headingTag" class="post-item__title h3">
					<a href="{!! $vacancy->permalink() !!}">
						{!! surfGetHeadingIcon('h3') !!}
						{!! $vacancy->title() !!}
					</a>
				</x-heading>
				<div class="post-item__meta-grouped">
					@if(is_archive())
						@if(!empty($vacancy->getHours()))
							<ul class="post-item__meta">
								<li>
									<x-icon icon="clock" sprite="global"/>
									{{ $vacancy->getHours() }}
								</li>
							</ul>
						@endif
						@if(!empty($vacancy->getSalary()))
							<ul class="post-item__meta">
								<li>
									<x-icon icon="euro" sprite="global"/>
									{{ $vacancy->getSalary() }}
								</li>
							</ul>
						@endif
					@endif
				</div>
				@if($category && is_archive())
					<ul class="post-item__meta post-item__meta--category">
						<li>
							<x-icon icon="tag" sprite="global"/>
							{{ $category }}
						</li>
					</ul>
				@endif
				<p>{!! surfGetMyExcerpt(15, $vacancyID, '...') !!}</p>
			</div>

			@if(has_post_thumbnail($vacancyID))
				<div class="post-item__figure">
					@if($vacancy->categories()->pluck('name')->join(''))
						<div class="post-item__category post-item__category--float">{{ $vacancy->categories()->pluck('name')->join('') }}</div>
					@endif
					<a href="{{ $vacancy->permalink() }}" rel="bookmark">
						{!! $vacancy->postThumbnail('post-image-full') !!}
					</a>
				</div>
			@endif
	</div>

	@include('vacancy.schema', compact('vacancy'))
</div>
