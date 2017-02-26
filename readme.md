# VIZU
Simple, dependency-free CMS system that allows for quick implementation of simple Web pages without having to configure anything in the administration panel.


## Instalation

## Creating theme

1. Create directory in themes/ - it will be your theme name.
2. Create direcory "templates" inside your theme directory.
3. HTML code should be put in "home.phtml" in "templates" directory.
4. Avery other aspect of your theme depends on your preferences.

### Adding editable Field

``{{ field_type param1='value 1' param2='value 2' }}``

### Field types

* `{{ text }}` - insert text that can be edited in admin panel - content tab.
  - Available params: `type`, `id`, `name`, `desc`.
  - Available types: `simple` (simple text input), `rich` (rich text editor)
  - Example: `{{ text type='simple' id='slogan' name='Your slogan' desc='Put your motto here' }}`

* `{{ setting }}` - insert text that can be edited in admin panel - settings tab.
  - Available params: `type`, `id`, `name`, `desc`.
  - Available types: `simple` (simple text input), `rich` (rich text editor)

* `{{ lang }}` - insert text received from theme lang translations that are in themes/lang/xx.php.
  - Available params: `id`

### Predefined fields

* `{{ site_path }}`
* `{{ theme_path }}`