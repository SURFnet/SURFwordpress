@php
	use SURF\Helpers\PolylangHelper;

	/**
	 *  @var $dropdown
	 */

@endphp

@if(function_exists('pll_languages_list'))
	@if($dropdown == "true")
		<ul class="language-switch">
			<li>
                <span class="language-switch__current"><x-icon icon="globe" sprite="global"
                                                               class="language-switch__globe"/> {!! PolylangHelper::getCurrentLanguage() !!} <x-icon
			                icon="chevron-down" sprite="global" class="language-switch__chevron"/> </span>
				@endif
				<ul class="language-switch__list">
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
