# Template Controllers <!-- omit in toc -->

- [Default templates](#default-templates)
  - [Example](#example)
- [Page templates](#page-templates)
  - [Example](#example-1)
- [Dependency Injection](#dependency-injection)

## Default templates

WordPress uses a [Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/) system to determine which template file should be loaded depending on certain variables like the post type, wether it's an archive/single page and some other factors. To make this system compatible with our Blade templating and to provide a solid way for separation of concerns (MVC, SOLID) the starter-theme gives you the ability to create Controllers in these templates.

### Example

To create a controller for the single template for the surf-event post type you would create the `single-surf-event.php` file in the theme root.

The class name of the controller should be a pascal case version of the filename with `Controller` at the end.

| File name              | Controller class           |
|------------------------|----------------------------|
| single.php             | SingleController           |
| archive.php            | ArchiveController          |
| single-post-type.php   | SinglePostTypeController   |
| archive-post-type.php  | ArchivePostTypeController  |
| single-surf-event.php  | SingleSURFEventController  |
| archive-surf-event.php | ArchiveSURFEventController |

The controller class should extend the `TemplateController` class and have a `handle` method. The handle method gets called when the template is included.

```php
<?php

namespace SURF;

use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Agenda;

class SingleSURFEventController extends TemplateController
{
    public function handle()
    {
        return $this->view('single-surf-event');
    }
}
```

## Page templates

Custom page templates work in the same way as the default templates. The only difference being the location of the file and the comment at the top specifying the template name.

### Example

To create a home template we would first create the `page-templates` directory in the theme root. In this directory we create the `template-home.php` file that contains the `TemplateHomeController` class.

```php
<?php
/**
 * Template name: Home
 */

namespace SURF;

use SURF\Core\Controllers\TemplateController;

class TemplateHomeController extends TemplateController
{
    public function handle()
    {
        return $this->view('home');
    }
}
```

## Dependency Injection

Using some magic you can use [Dependency Injection](https://laravel.com/docs/8.x/container#automatic-injection) to automatically retrieve dependencies and data in in the arguments of `handle`. This means that you can leave the argument list empty, but you can also add any number of arguments, and those will get automatically resolved and injected.

In the example below we specify that we want an `Event` object in the `$event` argument. Since this is the controller for the event single template the `$event` variable will contain the current post that you would normally retrieve using the `the_post` function.

```php
<?php

namespace SURF;

use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Agenda;

class SingleSURFEventController extends TemplateController
{
    public function handle(Agenda $event)
    {
        return $this->view('single-surf-event', ['event' => $event]);
    }
}
```
