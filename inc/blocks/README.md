## Blocks

Supported blocks within this plugin are auto loaded based on the block name in JS.

To add support to the this plugin for a block from a 3rd party, include a new directory, as well as a PHP class file that matches the following convention:

```
blocks
│   README.md # this file
│
└───core
│   │   class-paragraph.php # core/paragraph block
│   │   class-heading.php # core/heading block
│
└───jetpack
│   │   class-form.php # jetpack/form block

```

If you need the override this mapping, you can add an exception in:

```php
WP_Component_Library\Block_Parser::build_class_string();
```

If many exceptions need to be made, it may be worthwhile to add a filter to the returned output for better organization.
