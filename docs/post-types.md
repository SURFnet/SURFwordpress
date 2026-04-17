# Post Types <!-- omit in toc -->

- [How to register a new post type](#how-to-register-a-new-post-type)
- [How to register ACF fields for a post type](#how-to-register-acf-fields-for-a-post-type)
- [How to use a post in a template](#how-to-use-a-post-in-a-template)
- [Adding custom methods to a post type](#adding-custom-methods-to-a-post-type)
- [Custom Queries](#custom-queries)

## How to register a new post type

To register a new post type you can create a new class in `includes/PostTypes` with the following content.

```php
<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\Registers;

/**
 * Class Event
 * @package SURF\PostTypes
 */
class Event extends BasePost
{
    use Registers;

    protected static string $postType = 'surf-event';
}
```

Done! Your post type is now registered. However, most settings for the post type are derived from its class name.
You can customize these settings by overwriting some static methods.

```php
<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\Registers;

/**
 * Class Event
 * @package SURF\PostTypes
 */
class Event extends BasePost
{
    use Registers;

    protected static string $postType = 'surf-event';

    public static function getSlug(): string
    {
        return _x('events', 'post-type slug', 'van-ons');
    }

    public static function getSingularLabel(): string
    {
        return __('Event', 'van-ons');
    }

    public static function getPluralLabel(): string
    {
        return __('Events', 'van-ons');
    }

    public static function getArgs(): array
    {
        return ['menu_icon' => 'dashicons-calendar-alt'];
    }
}
```


## How to register ACF fields for a post type

We can register ACF fields to a post type by adding the `HasFields` trait and overwriting the static method `getFields`.
Here we can return an array with ACF settings that will get registered using `acf_add_local_field_group`.
You'll notice that we don't have to provide the `location` data, this will get automatically injected before registering the fields.

Notice that you can return multiple arrays that contain ACF data. This makes it possible to register multiple ACF field groups for a post type.

```php
<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFields;
use SURF\Core\PostTypes\Registers;

/**
 * Class Event
 * @package SURF\PostTypes
 */
class Event extends BasePost
{
    use Registers, HasFields;

    protected static string $postType = 'surf-event';

    ...

    public static function getFields(): array
    {

        $settings = [
            'key' => 'group_event_settings',
            'title' => __('Event settings', 'van-ons'),
            'fields' => [
                [
                    'key' => 'field_start_date',
                    'label' => __('Start date', 'van-ons'),
                    'name' => 'start_date',
                    'type' => 'date_picker'
                ],
                [
                    'key' => 'field_end_date',
                    'label' => __('End date', 'van-ons'),
                    'name' => 'end_date',
                    'type' => 'date_picker'
                ]
            ]
        ];

        return [$settings];
    }
}
```

## How to use a post in a template

The post classes also serve as ViewModels, they contain methods that will return the output of most post-related WordPress functions like `the_title`, `the_content`, `the_ID` and more.
These methods are defined in the `HasTemplateTags` trait so feel free to add more if you are missing something!

|WordPress function|Post method|
|------------------|-----------|
|`the_content()`|`$post->content()`|
|`the_title()`|`$post->title()`|
|`the_ID()`|`$post->ID()`|
|`the_excerpt()`|`$post->excerpt()`|
|`the_permalink()`|`$post->permalink()`|
|`the_tags()`|`$post->tags()`|
|`the_category()`|`$post->category()`|
|`the_date()`|`$post->date()`|
|`the_time()`|`$post->time()`|
|`the_modified_date()`|`$post->modifiedDate()`|
|`the_modified_time()`|`$post->modifiedTime()`|
|`has_post_thumbnail()`|`$post->hasPostThumbnail()`|
|`the_post_thumbnail()`|`$post->postThumbnail()`|
|`post_class()`|`$post->postClass()`|
|`edit_post_link()`|`$post->editPostLink()`|
|`comments_open()`|`$post->commentsOpen()`|
|`get_comments_number()`|`$post->getCommentsNumber()`|
|`comments_template()`|`$post->commentsTemplate()`|


```html
<article>
    <h2>{!! $event->title() !!}</h2>
    <div>
        {!! $event->content() !!}
    </div>
    <div>
        {{ $event->getMeta('start_date') }}
        {{ $event->getMeta('end_date') }}
    </div>
</article>
```

## Adding custom methods to a post type

The post type models have a convenient `getMeta` method that makes getting metadata for that specific post easy.
Sometimes, however, you will need access to some more complex data that can not easily be retrieved inside a template.
In those cases you can add custom (non-static) methods to a post type class. Those methods will be acessible on any instance of that post type.

```php
<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFactory;
use SURF\Core\PostTypes\HasFields;
use SURF\Core\PostTypes\Registers;

/**
 * Class Event
 * @package SURF\PostTypes
 */
class Event extends BasePost
{
    use Registers, HasFactory, HasFields;

    protected static string $postType = 'surf-event';

    public function startDate(string $format = 'Y-m-d H:i:s'): string
    {
        return wp_date(
            $format,
            strtotime($this->getMeta('start_date'))
        );
    }

    public function endDate(string $format = 'Y-m-d H:i:s'): string
    {
        return wp_date(
            $format,
            strtotime($this->getMeta('end_date'))
        );
    }
    ...
}
```

## Custom Queries

When you need a custom query to retrieve a collection of posts you can call the static `query` method on a post type class.

This method takes exactly the same arguments array as the `WP_Query` class, so check out the [official WordPress documentation](https://developer.wordpress.org/reference/classes/wp_query/) to see what arguments you can use.

The `query` method will return a `PostCollection` instance containing the results of the query. You can use this object as you would use a normal array, but it has some extra functionality that makes working with a collection easier than working with normal arrays.

`PostCollection` extends the `Collection` class that is used in Laravel, you can check the [official Laravel documention](https://laravel.com/docs/collections#available-methods) to read more about them.

```php
$collection = Event::query([
    'posts_per_page' => 5,
    // Etc...
]);
```

In case you want to query multiple post types, you can use the `PostCollection::fromQuery` method directly. The `PostCollection` class will make sure that each post will get mapped to the correct post type class.

Note that the `PostCollection::fromQuery` can take either a `WP_Query` instance, or just the array of arguments for a query.

```php
$collection = PostCollection::fromQuery([
    'post_type' => ['post', 'page'],
    'author' => 1,
    // Etc...
]);
```

