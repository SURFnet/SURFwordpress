# Taxonomies <!-- omit in toc -->

- [How to register a taxonomy](#how-to-register-a-taxonomy)
- [How to register ACF fields for a taxonomy](#how-to-register-acf-fields-for-a-taxonomy)
- [Adding custom methods to a taxonomy](#adding-custom-methods-to-a-taxonomy)

## How to register a taxonomy

To register a new taxonomy you can create a new class in the `includes/Taxonmies` directory with the following content:

```php
<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Agenda;

class Location extends Taxonomy
{
    use Registers;

    protected static string $taxonomy = 'surf-location';

    protected static array $postTypes = [Agenda::class];

    public static function getSlug(): string
    {
        return _x('location', 'taxnomy slug', 'van-ons');
    }

    public static function getSingularLabel(): string
    {
        return __('Location', 'van-ons');
    }

    public static function getPluralLabel(): string
    {
        return __('Locations', 'van-ons');
    }
}
```

In this class we create a `Location` taxonomy. By using the `SURF\Core\Taxonomies\Registers` trait we make sure that the taxonomy gets registered.
We can control the configuration for this taxonomy with some static methods that this trait can use:

- `getSlug` - Returns the taxonomy slug
- `getSingularLabel` - Returns the singular label
- `getPluralLabel` - Returns the plural label
- `getArgs` - Returns any other arguments that should get passed to `register_taxonomy` function call.

## How to register ACF fields for a taxonomy

Similarly to post types we can register ACF fields to a taxonomy by adding the `HasFields` trait and overwriting the static method `getFields`.
In this method we can return an array with ACF settings that will get registered using `acf_add_local_field_group`.
You'll notice that we don't have to provide the `location` data, this will get automatically injected before registering the fields.

Notice that you can return multiple arrays that contain ACF data. This makes it possible to register multiple ACF field groups for a taxonomy.

```php
<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\HasFields;
use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Agenda;

class Location extends Taxonomy
{
    use Registers, HasFields;

    protected static string $taxonomy = 'surf-location';

    protected static array $postTypes = [Agenda::class];

    ...

    public static function getFields(): array
    {
        $settings = [
            'key' => 'group_location_settings',
            'title' => __('Location settings', 'van-ons'),
            'fields' => [
                [
                    'key' => 'field_location_province',
                    'label' => __('Province', 'van-ons'),
                    'name' => 'province',
                    'type' => 'text'
                ]
            ]
        ];

        return [$settings];
    }
}
```

## Adding custom methods to a taxonomy

The taxonomy models have a convenient `getMeta` method that makes getting metadata for that specific term easy.
Sometimes, however, you will need access to some more complex data that can't easily be retrieved inside a template.
In those cases you can add custom (non-static) methods to a taxonomy class. Those methods will be accessible on any instance of that term.

```php
<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\PostTypes\Agenda;

class Location extends Taxonomy
{
    use Registers, HasFields;

    protected static string $taxonomy = 'surf-location';

    protected static array $postTypes = [Agenda::class];

    ...

    public function getProvince(): string {
        return $this->getMeta('province');
    }
}

```

