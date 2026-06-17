@php
	use SURF\Enums\Theme;
	use SURF\PostTypes\Vacancy;
	use SURF\Taxonomies\VacancyCategory;

	/**
	 * @var Vacancy $vacancy
	 * @var $relatedEvents
	 */

	$postID = $vacancy->ID();
	$termID = $vacancy->getPrimaryCategoryId($postID);
	$primaryCategory = VacancyCategory::find($termID);
@endphp

@extends('layouts.app')

@section('content')
	<article id="post-{{$postID}}"
	         {!! $vacancy->postClass('entry') !!} @if(Theme::isSURF() && $vacancy->getPrimaryCategoryColor($postID))style='--surf-color-category: {{ $vacancy->getPrimaryCategoryColor($postID) }};'@endif>
		<div class="entry__header entry__header--full-with container padded">
			<x-breadcrumb/>
			<header class="vacancy__header">
				<h1 class="entry__title">
					{!! surfGetHeadingIcon('h1') !!}
					{!! $vacancy->title() !!}
				</h1>
				@php
					do_action( 'SURF/ApplicationController.applyButton', $postID );
				@endphp
			</header>
			<ul class="entry__meta">
				<li class="entry__meta-category">
					<x-icon icon="tag" sprite="global"/>
					@if($primaryCategory)
						@php $showClosingATag = true; @endphp
						<a href="{{ $primaryCategory->link() }}">
							@endif
							{{ $primaryCategory->name }}
							@if($showClosingATag ?? false)
						</a>
					@endif
				</li>
				@if($vacancy->getPrimaryHoursName($postID))
					<li>
						<x-icon icon="clock" sprite="global"/>
						{{ $vacancy->getPrimaryHoursName($postID) }}
					</li>
				@endif
				@if($vacancy->getSalary())
					<li>
						<x-icon icon="euro" sprite="global"/>
						{{ $vacancy->getSalary() }}
					</li>
				@endif
			</ul>
		</div>

		<div class="padded container grid">
			<div class="entry__inner column span-8-lg span-4-md span-4-sm">
				{!! $vacancy->content() !!}
			</div>
			<div class="vacancy__sidebar entry__inner column span-4-lg span-4-md span-4-sm">
				@if($vacancy->shouldShowMeta())
					<div class="vacancy__meta">
						<dl>
							@if(!empty($vacancy->getLocation()))
								<dt>{{ __('Location', 'wp-surf-theme') }}</dt>
								<dd>{{ $vacancy->getLocation() }}</dd>
							@endif
							@if(!empty($vacancy->getDegree()))
								<dt>{{ __('Degree', 'wp-surf-theme') }}</dt>
								<dd>{{ $vacancy->getDegree() }}</dd>
							@endif
							@if(!empty($vacancy->getEmployment()))
								<dt>{{ __('Employment', 'wp-surf-theme') }}</dt>
								<dd>{{ $vacancy->getEmployment() }}</dd>
							@endif
							@if(!empty($vacancy->getSalary()))
								<dt>{{ __('Salary', 'wp-surf-theme') }}</dt>
								<dd>{{ $vacancy->getSalary() }}</dd>
							@endif
							@if($vacancy->getPrimaryHoursName($postID))
								<dt>{{ __('Hours per week', 'wp-surf-theme') }}</dt>
								<dd>{{ $vacancy->getPrimaryHoursName($postID) }}</dd>
							@endif
							@if(!empty($vacancy->getDeadline()))
								<dt>{{ __('Deadline', 'wp-surf-theme') }}</dt>
								<dd>{{ $vacancy->getDeadline() }}</dd>
							@endif
							@if(!empty($vacancy->getContact()))
								@foreach($vacancy->getContact() as $contact)
									<dt>{{ $contact['title'] }}</dt>
									<dd>{{ $contact['person'] }}
										@if(!empty($contact['email']))
											<br> <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>
										@endif
										@if(!empty($contact['phone']))
											<br> <a href="tel:{{ $contact['phone'] }}">{{ $contact['phone'] }}</a>
										@endif
									</dd>
								@endforeach
							@endif
						</dl>
					</div>
				@endif

				@if($conditions = $vacancy->getEmploymentConditions())
					<div class="vacancy__meta vacancy__meta--conditions">
						<div class="vacancy__meta__conditions">
							{!! $conditions !!}
						</div>
						@if($disclaimer = $vacancy->getEmploymentConditionsDisclaimer())
							<div class="vacancy__meta__conditions-disclaimer">
								{!! $disclaimer !!}
							</div>
						@endif
					</div>
				@endif
			</div>
		</div>
		<div class="padded container">
			@php
				do_action( 'SURF/ApplicationController.create', $postID );
			@endphp
		</div>
	</article><!-- #post-{{ $postID }} ?> -->

	{!! $vacancy->editPostLink(text: _x('Edit', 'admin', 'wp-surf-theme'), class: 'post-edit-link button') !!}

	<div class="container padded">
		@include('components.share')
	</div>

	@include('vacancy.schema', compact('vacancy'))
@endsection
