# Templax - The simple, hookable render framework for Php

## Quickstarter:

```html
<!-- index.html -->
<html>
    <head>
        <title>Templax</title>
    </head>
    <body>
        <h1>{{ title }}</h1>
        <p>{{ content }}</p>
    </body>
</html>
```

```php
// adjust your path to the Templax.php
require_once( "your/path/to/templax.php" );

use \Templax\Templax;

// initialize framework
Templax::Init( array(
    "template" => __DIR__ . "/index.html"
));

// markup to define what values are used for the marker
$markup = array(
    "title" => "Templax",
    "content" => "my first template"
);

// parse content
$content = Templax::parse( "templax", $markup );

echo $content;
```