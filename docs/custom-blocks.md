# Custom Blocks <!-- omit in toc -->

- [Registering a block](#registering-a-block)

## Registering a block

Registering blocks is easy. First you create a new class in the `includes/Blocks` directory. This class should extend the `Block` abstract class.

```php
<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;

class ExampleServerSideBlock extends Block {}
```

When you are creating a JavaScript block that's it! Everything gets registered automatically. Of course you can still overwrite the base methods of the `Block` class to customize it's behaviour, but that is optional.

If you are creating a serverside block you will need to create a blade file in the `views/blocks` directory with the kebab-cased name of the block e.g. `ExampleServerSideBlock` will need a `views/blocks/example-server-side-block.blade.php` view.

The view has access to the `$blockAttributes`, `$blockName` and `$content` attributes. You can customize this behaviour by overwriting the `render` method of the block.

```php
@php
/**
 * @var array $blockAttributes
 * @var string $content
 * @var string $blockName
 */
@endphp

<h2>{{ __('Example Server Side Block', 'van-ons') }}</h2>

...
```
