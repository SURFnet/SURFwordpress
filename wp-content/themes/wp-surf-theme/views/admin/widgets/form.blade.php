@php
	/**
	 * @var WP_Widget $widget
	 * @var array $instance
	 * @var array $formArgs
	 * @var bool $showTitle
	 */
@endphp

<p>
	<label for="{{ $widget->get_field_id('form') }}">{{ _x('Select a form', 'admin', 'wp-surf-theme') }}:</label>
	{!! acf_select_input($formArgs) !!}
</p>

<p>
	{!! acf_checkbox_input(array_merge(
			[
				'id' => $widget->get_field_id('show_title'),
				'name' => $widget->get_field_name('show_title'),
				'label' => _x('Show title', 'admin', 'wp-surf-theme'),
			],
			$showTitle ? ['checked' => true] : [])
	) !!}
</p>
