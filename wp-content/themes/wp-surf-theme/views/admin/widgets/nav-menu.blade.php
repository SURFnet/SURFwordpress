@php
	/**
	 * @var WP_Widget $widget
	 * @var array $instance
	 * @var array $menuArgs
	 */
@endphp

<p>
	<label for="{{ $widget->get_field_id('menu') }}">{{ _x('Select a menu', 'admin', 'wp-surf-theme') }}:</label>
	{!! acf_select_input($menuArgs) !!}
</p>
