@php
	/**
	 * @var WP_Widget $widget
	 * @var array $instance
	 * @var array $socials
	 */
@endphp

<p>
	<label for="{{ $widget->get_field_id('title') }}">{{ _x('Title', 'admin', 'wp-surf-theme') }}:</label>
	<input id="{{ $widget->get_field_id('title') }}" name="{{ $widget->get_field_name('title') }}" type="text"
	       class="widefat" value="{{ $instance['title'] ?? '' }}">
</p>

@foreach ($socials as $key => $social)
	<p>
		{!! acf_checkbox_input(array_merge(
			[
				'id' => $widget->get_field_id("{$key}_show"),
				'name' => $widget->get_field_name("{$key}_show"),
				'label' => $social['label'],
			],
			($instance["{$key}_show"] ?? '') === 'on' ? ['checked' => true] : [])
		) !!}
	</p>
@endforeach
