# Blade <!-- omit in toc -->

- [POEdit](#poedit)
- [Cheatsheet](#cheatsheet)

We use Laravels templating engine 'Blade' for templating in our WordPress projects (starting from v4.0.0 of the starter theme).
This documentation serves as a quick reminder of some basic functions in Blade. For more information you can reference [the official Laravel Blade documentation](https://laravel.com/docs/blade).

## POEdit

Currently, POEdit cannot find translation functions (__, _e, etc...) in blade files when they're used in curly braces or blade directives.
The solution to this issue is to compile the views before scanning for translatable strings with POEdit. There are multiple ways you can do this.
All of these methods expect that you have run `composer install` in the project root.

- `npm run compile-views` - Manually compile the views
- `npm run poedit` - Compile the views and open POEdit with the `nl_NL.po` file opened (MacOS only)
- `npm run watch` - After every update the views get compiled automatically

## Cheatsheet
```
{{ $var }} - Echo content, this automatically escapes HTML
{!! $var !!} - Echo content without escaping HTML

{{-- Comment --}} - A Blade comment

@extends('layout') - Extends a template with a layout

@if(condition) - Starts an if block
@else - Starts an else block
@elseif(condition) - Start a elseif block
@endif - Ends a if block

@foreach($list as $key => $val) - Starts a foreach block
@endforeach - Ends a foreach block

@forelse($list as $key => $val) - Starts a forelse block
@empty - Block to render when $list is empty
@enforelse - Ends a forelse block

@isset($val) - Start a block that only gets rendered if isset($val) is true
@endisset - End an isset block

@empty($val) - Start a block that only gets rendered if empty($val) is true
@endempty - End an empty block

@for($i = 0; $i < 10; $i++) - Starts a for block
@endfor - Ends a for block

@while(condition) - Starts a while block
@endwhile - Ends a while block

@unless(condition) - Starts an unless block
@endunless - Ends an unless block

@include(file) - Includes another template
@include(file, ['var' => $val,...]) - Includes a template, passing new variables.

@each('file', $list, 'item') - Renders a template on a collection
@each('file', $list, 'item', 'empty') - Renders a template on a collection or a different template if collection is empty.

@yield('section') - Yields content from a section in a layout
@section('name') - Starts a section
@endsection - Ends section

@stack('scripts) - Create a stack in a layout. This is similar to @yield and @section with the exception that a stack can have multiple @push or @prepend blocks.
@push('scripts') - Add content to the end of a stack
@endpush - Ends a push block
@prepend('scripts') - Add content to the beginning of a stack
@endprepend - Ends a prepend block

@php - Start a PHP block, here you can write any normal PHP code that you want (you probably shouldn't though)
@endphp - Ends a PHP block
```
