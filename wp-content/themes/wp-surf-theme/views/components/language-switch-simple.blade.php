@php
	use SURF\Helpers\PolylangHelper;

	$class = $class ?? '';
@endphp

@if(function_exists('pll_languages_list'))
	<nav @class([$class])>
		<ul>
			{!!PolylangHelper::theLanguagesSlugs()!!}
		</ul>
	</nav>
@endif
