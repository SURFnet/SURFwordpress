@php
	use SURF\Services\CspService;

	$service = new CspService();
@endphp

<div class="wrap">
	<h1>{{ _x('CSP Tool', 'admin', 'wp-surf-theme') }}</h1>

	@if(is_bool($status) && $status)
		<div class="notice notice-success">
			<p>{{ _x('Up to date!', 'admin', 'wp-surf-theme') }}</p>
		</div>
	@elseif(is_bool($status) && !$status)
		<div class="notice notice-danger">
			<p>{{ _x('Something went wrong while syncing the new config', 'admin', 'wp-surf-theme') }}</p>
		</div>
	@endif

	<table class="form-table">
		<tbody>
		<tr>
			<th scope="row">
				<label for="current-csp-config">{{ _x('Current CSP Config', 'admin', 'wp-surf-theme') }}</label>
			</th>
			<td>
                    <textarea id="current-csp-config" name="csp" id="csp" cols="100" rows="20"
							  readonly>{{ json_encode( $service->getCurrentCspConfig() ) }}</textarea>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="csp-config">{{ _x('Sync CSP config', 'admin', 'wp-surf-theme') }}</label>
			</th>
			<td>
				<form action="" method="post">
					@php
						wp_nonce_field( 'sync_csp_config' );
					@endphp
					<input type="hidden" name="action" value="sync_csp_config">
					<input class="button primary" type="submit"
						   value="{{ _x('Sync CSP config', 'admin', 'wp-surf-theme') }}">
				</form>
			</td>
		</tr>
		</tbody>
	</table>
</div>
