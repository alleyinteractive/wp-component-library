# WP Component Library

A plugin to list and preview components from the active theme's component
library in the WordPress admin. Also provides helper functions for loading
components in your theme so you can build with components atomically, similar
to how you can work with components in React.

## Usage

This plugin will add a new area to the WordPress admin called
"Component Library." By default, this area will be accessible to anyone with
the `manage_options` capability, but this is filterable.

Upon visiting the Component Library page, any components configured in the
active theme's `components` folder within the root of the theme according to
the standards laid out below will be displayed with information from the
README and one or more live previews based on values in the `examples` key in
the `component.json` file in the component folder.

## Directory Structure

In order for this plugin to pick up and display your components, you need to
establish the following directory structure:

* wp-content
  * themes
    * {theme-name}
      * components
        * {component-name}
          * component.json - A JSON file containing a title for the component,
            props for the component, and example data for admin previews. See
            below for more info.
          * README.md - An explanation of what the component is, what props it
            accepts, and how it should be used.
          * style.scss - Optional. A stylesheet that defines styles for the
            component. See the Loading Styles section below for more info.
          * template.php - The file that renders the component.

For examples, look in the
[components directory](components).

### `component.json`

A JSON file with three top-level keys: `examples`, `props`, and `title`.

#### `examples`

An array of objects, each of which should have a `title` property that defines
the title for the example in the admin, and a `props` property that contains
an object with key/value pairs for prop values.

#### `props`

An array of objects defining props for the component, the spec for which is as follows:

| Property       | Default  | Required | Type   | Description                                                                               |
|----------------|----------|----------|--------|-------------------------------------------------------------------------------------------|
| allowed_values | []       | No       | array  | If the `type` is set to `enum`, the list of allowed values.                               |
| default        | ''       | No       | string | The default value to use if none is provided in component props.                          |
| description    |          | Yes      | string | The description of the component to display in the admin.                                 |
| required       | false    | No       | bool   | Whether a value for the prop is required or not.                                          |
| type           | 'string' | No       | string | The prop type. Possible values are 'array', 'bool', 'enum', 'number', 'object', 'string'. |

#### `title`

A string that names the component when it is viewed in the admin. For example,
"Button."

#### Example

An example `component.json` can be found in
[tests/components/button/component.json](tests/components/button/component.json).

### `README.md`

The file that describes your component. The examples and props sections of the
documentation will be dynamically compiled from the definitions in
`component.json`, but the README will be used for a long description of what
the component is used for.

#### Example

An example `README.md` can be found in
[tests/components/button/README.md](tests/components/button/README.md).

### `style.scss`

A stylesheet that contains styles that are scoped to the component. Whether and
how you work with these stylesheets is up to you, as they will need to be
integrated into your theme's asset builder. However, globbing them using
[node-sass-glob-importer](https://www.npmjs.com/package/node-sass-glob-importer)
is a good idea, as it will allow you to create styles for new components simply
by adding a stylesheet to a directory rather than needing to import the
stylesheet into an index file.

#### Example

An example `style.scss` can be found in
[tests/components/button/style.scss](tests/components/button/style.scss).

### `template.php`

The template file behaves like a normal template file called by
`get_template_part`. It has access to all of the same variables. However, the
props system will enforce props rules and set default values for you, so the
`$args` variable that contains the arguments for the template is a little
smarter than the WordPress default.

#### Example

An example `template.php` can be found in
[tests/components/button/template.php](tests/components/button/template.php).

## Templating Functions

This plugin contains a number of helpful functions for use in writing
WordPress templates using components that you define according to the specs
above.

### wpcl_component

The primary function used for working with components. Accepts two arguments:
a component name and an optional array of props. Loads and outputs the
component's template file after getting values for all props, either from what
was provided in the props array, or using default values for each prop.

#### Example Usage

```php
wpcl_component( 'button', [ 'href' => 'https://www.example.org', 'text' => 'Visit example.org' ] );
```

### wpcl_class

Accepts arguments in the same fashion as the
[classnames npm package](https://www.npmjs.com/package/classnames). If there is
at least one class name in the list, prints a `class` attribute with the list
of classnames, properly escaped. If there are no classes, does not print
anything. Useful for applying multiple classes, conditional classes, derived
classes, and merging a set of classes with class names that may have been passed
to a component via props.

#### Example Usage

```php
wpcl_class( 'class-1', [ 'class-2', 'class-3' ], [ 'class-4' => $maybe_include ], $args['class'] );
```

### wpcl_id

Similar to `wpcl_class`, accepts an ID parameter and conditionally outputs an
`id` attribute. Useful when adding support for the `id` prop to a component.

#### Example Usage

```php
wpcl_id( $args['id'] );
```

### wpcl_markdown

Safely converts a markdown string to HTML and outputs it.

#### Example Usage

```php
wpcl_markdown( '## This will become an h2' );
```
