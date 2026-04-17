@php
	/**
	 * @var WP_Widget $widget
	 * @var array $instance
	 * @var array $stores
	 */
@endphp

<p>
	<label for="{{ $widget->get_field_id('title') }}">{{ _x('Title', 'admin', 'wp-surf-theme') }}:</label>
	<input id="{{ $widget->get_field_id('title') }}" name="{{ $widget->get_field_name('title') }}" type="text"
	       class="widefat" value="{{ $instance['title'] ?? '' }}">
</p>

@foreach ($stores as $key => $store)
	<p><strong>{{ $store['label'] }}</strong></p>
	<p>
		{!! acf_checkbox_input(array_merge(
			[
				'id' => $widget->get_field_id("{$key}_show"),
				'name' => $widget->get_field_name("{$key}_show"),
				'label' => _x('Enabled', 'admin', 'wp-surf-theme'),
			],
			($instance["{$key}_show"] ?? '') === 'on' ? ['checked' => true] : [])
		) !!}
	</p>
	<p>
		<label for="{{ $widget->get_field_id("{$key}_url") }}">{{ _x('URL', 'admin', 'wp-surf-theme') }}:</label>
		<input id="{{ $widget->get_field_id("{$key}_url") }}" name="{{ $widget->get_field_name("{$key}_url") }}"
		       type="url" class="widefat" value="{{ $instance["{$key}_url"] ?? '' }}">
	</p>
@endforeach
