@php
	use SURF\Helpers\PolylangHelper;

	/**
	 *  @var $dropdown
	 */

	// The header can contain more than one switch, so the list gets a unique ID for aria-controls
	$listId = wp_unique_id( 'language-switch-list-' );

@endphp

@if(function_exists('pll_languages_list'))
	@if($dropdown == "true")
		<ul class="language-switch" data-language-switch>
			<li>
				<button type="button" class="language-switch__current" aria-expanded="false" aria-controls="{{ $listId }}" data-language-switch-toggle>
					<x-icon icon="globe" sprite="global" class="language-switch__globe"/>
					<span class="sr-only">{{ __('Switch language', 'wp-surf-theme') }}:</span>
					{!! PolylangHelper::getCurrentLanguage() !!}
					<x-icon icon="chevron-down" sprite="global" class="language-switch__chevron"/>
				</button>
				@endif
				<ul class="language-switch__list" @if($dropdown == "true") id="{{ $listId }}" @endif>
					@if($dropdown == "false")
						<li class="language-switch__title">
							<x-icon icon="globe" sprite="global"
							        class="language-switch__globe"/> {{__('Switch language', 'wp-surf-theme')}}</li>
					@endif
					{!! PolylangHelper::theLanguages() !!}
				</ul>
				@if($dropdown == "true")
			</li>
		</ul>
	@endif
@endif
