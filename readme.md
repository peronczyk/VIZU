# VIZU 1.0.0 beta
Simple, dependency-free CMS system that allows for quick implementation of simple Web pages without having to configure anything in the administration panel.


## Installation
1. Import file `install/db.sql` in to your MySQL database.
2. Provide database connection credentials in `config.php` file.
3. Remove `install/` directory.
4. Head to administration panel _yourwebsite.com/**admin/**_ and change default user credentials. Login data: 'admin@website.com' / 'admin'.

## Creating theme

1. Create directory in themes/ - it will be your theme name.
2. Create direcory "templates" inside your theme directory.
3. HTML code should be put in "home.html" in "templates" directory.
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
  - Example: `{{ setting type='rich' id='site_title' name='Your slogan' desc='Put your motto here' }}`

* `{{ lang }}` - insert text received from theme lang translations that are in themes/lang/xx.php.
  - Available params: `id`
  - Example: `{{ lang id='read_more' }}`


### Field editor types

Editor types are defined in theme with `type='<type>'` param. Available types:
* `simple` - simple input without ability to style inserted text
* `rich` - advanced WYSIWYG editor that allows to add styling to inserted text

### Predefined fields

* `{{ site_path }}`
* `{{ theme_path }}`