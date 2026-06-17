# ACF Blocks <!-- omit in toc -->

- [Registering a block](#registering-a-block)
- [Adding fields](#adding-fields)
- [More configuration](#more-configuration)
  - [Adding a property](#adding-a-property)
  - [Overriding a method](#overriding-a-method)

## Registering a block

Registering blocks is easy. First you create a new class in the `includes/Blocks` directory. This class should extend the `AcfBlock` abstract class.

```php
<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\AcfBlock;

class ExampleAcfBlock extends AcfBlock
{
    ...
}
```

## Adding fields

To add fields we can override the `getFields()` method. This method should return an array in the same format as any ACF field group takes the `fields` array. See the [ACF Docs](https://www.advancedcustomfields.com/resources/register-fields-via-php) for more information.

```php
<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\AcfBlock;

class ExampleAcfBlock extends AcfBlock
{
    public function getFields(): array
    {
        return [
            [
                'key' => $this->getFieldKey('title'),
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ],
            [
                'key' => $this->getFieldKey('image'),
                'label' => 'Image',
                'name' => 'image',
                'type' => 'image',
            ]
        ];
    }
}

```

## More configuration

The block gets configurated with a lot of defaults that are based on the classname. You can change these options by adding a property or overriding a method.


### Adding a property

```php
<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\AcfBlock;

class ExampleAcfBlock extends AcfBlock
{
    protected ?string $name = 'block-name';
    protected ?string $title = 'Block Title';
    protected ?string $description = 'Block description';
    protected ?string $category = 'block-category';
    protected ?string $icon = 'block-icon';
    protected ?array $keywords = ['block', 'keywords'];
    ...
}

```

### Overriding a method

Sometimes you need some more control that is not possible by adding a property. For example, for a title or a description you might want to add a translation, and you can't call the `__` method when assigning a value to a property.

In these cases you can override the corresponding method. Check out the `AcfBlock` class to see what methods can be overriden. You can change every bit of data that gets registered in ACF this way.

```php
<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\AcfBlock;

class ExampleAcfBlock extends AcfBlock
{
    public function getTitle(): string
    {
        return __('Block Title', 'van-ons');
    }
}

```
