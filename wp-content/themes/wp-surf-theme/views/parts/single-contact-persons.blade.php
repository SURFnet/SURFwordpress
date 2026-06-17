@php
	use SURF\Helpers\PostHelper;

	$contacts = PostHelper::listContactPersons();
	if ( empty( $contacts ) ) {
		return;
	}

@endphp
<aside @class(['contact-persons', $class ?? ''])>
	<h2 @class(['sr-only'])>{{ __('Contact persons', 'wp-surf-theme') }}</h2>
	@foreach($contacts as $index => $contact)
		<x-contact-person :contactPerson="$contact" :variation="$index > 0 ? 'filled' : 'outlined'"/>
	@endforeach
</aside>
