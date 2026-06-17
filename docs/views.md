# Views <!-- omit in toc -->

- [Views](#views)
  - [Anywhere](#anywhere)
  - [In a controler](#in-a-controler)
  - [In a template](#in-a-template)
- [Passing data to a view](#passing-data-to-a-view)
  - [compact()](#compact)

## Views

Views are the files that we put in the `views` directory. They are templates that the Blade compiler reads and converts to standard PHP templates.

These views can be rendered with the `surfView` function, the first parameter for this function will be the view name. The view name is the path of the view file relative from the `views` directory and it uses `.` as a path seperator.

The second parameter will be an associative array of arguments that will be made available within the view.

### Anywhere

The starter-theme has the `surfView` helper function. This takes the name of a view and an associative array containing the variables that should be available in the view. It returns a `View` object, you can render the HTML by simply echoing or turning it into a string.

```php
<?= surfView('parts.event.item', ['item' => $event]) ?>

// OR

$html = surfView('parts.event.item')->render()
```

### In a controler

A TemplateController or a Controller for a custom route will render a view if you return one. To return a view you can use the `$this->view()` method.

```php
public function handle(Event $event)
{
    return $this->view('parts.event.item', ['item' => $event]);
}
```

Note: `$this->view()` just forwards the data to `surfView` so just calling that is also an option.

```php
public function handle(Event $event)
{
    return surfView('parts.event.item', ['item' => $event]);
}
```

### In a template

To render a view in a template you can use the `@include` blade directive. This takes the same arguments as the `surfView` method.

```php
@include('parts.event.item', ['item' => $event])
```

## Passing data to a view

All the mentioned render functions have the ability to pass an associative array of key-value pairs that will be available as variables in the view.
The code below demonstrates how you would pass an Event object to view that will be available by using the `$item` variable in the view itself.
```php
$event = Event::find(1);

// General
surfView('parts.event.item', ['item' => $event]);

// Controller
$this->view('parts.event.item', ['item' => $event]);

// Template
@include('parts.event.item', ['item' => $event]);
```

### compact()

Sometimes you will see the use of compact. This is an easy way to create an associative array from variables that are currently in scope.
Note that the name of the variable will be the value of the key in the resulting array.
```php
$item = Event::find(1);

// compact('item') === ['item' => $item]

// General
surfView('parts.event.item', compact('item'));

// Controller
$this->view('parts.event.item', compact('item'));

// Template
@include('parts.event.item', compact('item'));
```
