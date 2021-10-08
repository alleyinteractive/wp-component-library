# WP Component Library

A plugin to list and preview components from the active theme's component
library in the WordPress admin.

## Usage

This plugin will add a new area to the WordPress admin called
"Component Library." By default, this area will be accessible to anyone with
the `manage_options` capability, but this is filterable.

Upon visiting the Component Library page, any components configured in the
active theme's `components` folder within the root of the theme according to
the standards laid out below will be displayed with information from the
README and one or more live previews based on values in the `examples.json`
file in the component folder.

## Directory Structure

In order for this plugin to pick up and display your components, you need to
establish the following directory structure:

* wp-content
  * themes
    * {theme-name}
      * components
        * {component-name}
          * index.php - The file that renders the component.
          * README.md - An explanation of what the component is, what props it accepts, and how it should be used.
          * examples.json - A JSON file containing an array of objects representing props to pass to the component to generate example uses.
          * style.scss - Optional. A stylesheet that defines styles for the component. See the Loading Styles section below for more info.

// TODO: ADD PROPS DEFINITION

### Example index.php

TBD
