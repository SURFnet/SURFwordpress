<p>{{ _x('There is an update available for your theme, click the button below to update.', 'admin', 'wp-surf-theme') }}</p>

@if(surfApp()->isEnvironment('local', 'development'))
	<p>{!! _x('Notice: since you are on a dev environment the update will be installed in the <code>wp-content/updates</code> folder. This means you can safely press the update button without overwriting any files.', 'admin', 'wp-surf-theme') !!}</p>
@endif

<div data-component="ThemeUpdater" data-props="{{ json_encode(['nonce' => wp_create_nonce('wp_rest')]) }}"></div>
