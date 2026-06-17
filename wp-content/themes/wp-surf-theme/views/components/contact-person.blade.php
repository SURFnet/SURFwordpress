@props([
    'contactPerson' => null,
    'variation' => 'filled', // filled, outlined
])

@php
	use SURF\PostTypes\ContactPerson;

	/**
	 * @var ContactPerson $contactPerson
	 */

@endphp

<article @class(['contact-person', 'contact-person--' . $variation])>
	<div @class(['contact-person__inner'])>
		<div @class(['contact-person__content'])>
			<h3 @class(['contact-person__title'])>{{ $contactPerson->fullName() }}</h3>
			@if($contactPerson->has('description'))
				<p @class(['contact-person__description'])>{{ $contactPerson->description() }}</p>
			@endif
			@if($contactPerson->has('emailAddress'))
				<address @class(['contact-person__email'])>
					<x-icon :icon="'envelope'" :sprite="'global'"/>
					<a href="{{ $contactPerson->emailAddressUrl() }}">{{ $contactPerson->emailAddress() }}</a>
				</address>
			@endif
		</div>
		@if($contactPerson->has('pictureId'))
			<figure @class(['contact-person__picture'])>
				{!! $contactPerson->pictureMarkup('avatar', ['alt' => sprintf(__('Photo of %s', 'wp-surf-theme'), $contactPerson->fullName())]) !!}
			</figure>
		@endif
	</div>
</article>
