@php
	use Illuminate\Support\HtmlString;
	use Illuminate\View\ComponentAttributeBag;
	use SURF\Helpers\Helper;

	/**
	 * @var ComponentAttributeBag $attributes
	 * @var HtmlString $slot
	 */

	$name  = is_search() ? 's' : 'search';
	$value = Helper::getSanitizedRequest($name, '');

@endphp

<fieldset data-name="{{ $name }}" class="{{ $attributes->get('class') }}">
	<label for="archive-search" class="sr-only">{{ __('Search', 'wp-surf-theme') }}</label>
	<input id="archive-search" type="text"
	       name="{{ $name }}"
	       value="{{ $value }}"
	       placeholder="{{ $placeholder ?? null }}"/>
</fieldset>
