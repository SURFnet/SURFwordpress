@php
	/**
	 * @var string $theme_name
	 * @var string $version
	 * @var string $details
	 * @var array $info_list
	 * @var string $php_version
	 */
@endphp

<style>
	.surf-theme-modal {
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
		line-height: 1.5;
		margin: 2rem;
	}

	.surf-theme-content {
		display: flex;
		gap: 1.5rem;
		margin-top: 1.5rem;
	}

	.surf-theme-main {
		flex: 3;
	}

	.surf-theme-changelog {
		padding: 0 1.5rem 1.5rem 0;
	}

	.surf-theme-changelog ul {
		margin-left: 1.5rem;
		margin-bottom: 1rem;
	}

	.surf-theme-changelog code {
		background: #f1f1f1;
		padding: 2px 4px;
		border-radius: 3px;
	}

	.surf-theme-sidebar {
		flex: 1;
		background: #f6f7f7;
		padding: 0 2rem 1.5rem;
	}

	.surf-theme-sidebar ul {
		list-style: none;
		margin: .5rem 0 0;
		padding-left: 0;
	}

	.surf-theme-sidebar li {
		margin-bottom: 1rem;
	}
</style>
<div class="wrap surf-theme-modal">
	<h1 class="wp-heading-inline">{{ $theme_name }}</h1>
	<hr class="wp-header-end">

	<div class="surf-theme-content">
		<div class="surf-theme-main">
			<h2>{{ _x('Changelog', 'admin', 'wp-surf-theme') }}</h2>
			<div class="surf-theme-changelog">
				{!! $details !!}
			</div>
		</div>
		<div class="surf-theme-sidebar">
			@if( !empty($info_list) )
				<h2>{{ _x('Info', 'admin', 'wp-surf-theme') }}</h2>
				<ul>
					@foreach( $info_list as $item )
						<li>
							<strong>{{ $item['label'] }}</strong><br/>
							@if( ( $item['type'] ?? '' ) === 'html' )
								{!! $item['value'] !!}
							@else
								{{ $item['value'] }}
							@endif
						</li>
					@endforeach
				</ul>
			@endif
		</div>
	</div>
</div>