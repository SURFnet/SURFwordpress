# Blade Components <!-- omit in toc -->

- [Example](#example)

Blade offers a way to create components in a way that feels similar to JS libraries like React and Vue.
Please refer to the [Laravel Blade documentation](https://laravel.com/docs/blade#components) for more information.

## Example

To create an anonymous Blade component you can simply create a file in the `views/components` directory, eg: `card.blade.php`.

You can render the component by using it as an HTML element prefixed with `x-`

```html
 <x-card :title="$title">Content</x-card>
```

Attributes will be available as variables in the component, the child elements will be available as the `$slot` variable.

```html
<!-- views/components/card.blade.php -->

@php
/**
 * @var \SURF\Application $app
 * @var \Illuminate\View\ComponentAttributeBag $attributes
 * @var \Illuminate\Support\HtmlString $slot
 * @var string $title
 */
@endphp

<div class="card">
    <div class="card__title">
        {{$title}}
    </div>
    <div class="card__content">
        {{$slot}}
    </div>
</div>

```

You can also create a Component class in the `includes/View/Components` folder. Do this if you need some more control over the data that is available in the component template,
or if you have a lot of PHP logic that you do not want to run in your template file.

```php
// includes/Components/Card.php

namespace SURF\View\Components;

use Illuminate\View\Component;

class Card extends Component
{

    public ?string $title;

    public function __construct(string $title = null)
    {
        $this->title = $title;
    }

    public function render()
    {
        // You can run complicated logic here and pass it to the view
        $data = Something::complicated();

        return surfView('components.card', ['data' => $data]);
    }
}

```
