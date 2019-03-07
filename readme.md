# VIZU 2.0.2

The simplest landing page editor you can imagine. It is a dependency-free CMS system that allows for quick implementation of one-page websites without having to configure anything in the administration panel.

---
## Features:

* Works out of the box,
* very fast page load,
* easy installation without the need to configure a dedicated MySQL database,
* native multi-language support,
* native support for easy implementation of contact forms with reCaptcha 3 spam detection.

---
## Installation

1. By default VIZU uses SQLite database, which don't require any credentials to use. So to start installation process just head to _yourwebsite.com/**install**/_ and follow further instructions. The database will be created automaticcaly and will be filled with starting data. In the second step you will be asked to provide administrator account credentials.
2. After succesfull installation you can access administration panel by heading to _yourwebsite.com/**admin**/_ address.
3. You can also adjust website configuration in `config-override.php` file. List of available configuration entries are available in `config.php` file.

---
## Creating theme

1. Start with creating theme directory in _themes/_.
2. Create directory "templates" inside your theme directory.
3. HTML code should be put in "home.html" file located in "templates" directory (eg: /themes/yourtheme/templates/home.html).
4. Every other aspect of your theme depends on your preferences. You can use regular CSS, task runners etc.


### Simple example code of theme template:

```
<!DOCTYPE html>
<html lang="{{ lang_code }}">
    <head>
        <title>{{ simple id='page-title' name='Page title' }}</title>
    </head>

    <body>
        <header>
            <h1>{{ simple id='page-header' name='Page header' }}</h1>
            {{ rich id='page-description' name='Page description' }}
        </header>

        <main>
            <article>
                {{ rich id='article-content' name='Article content' }}
            </article>
        </main>

        <footer>
            <ul>
                {{ repeatable id='footer-links' name='Footer links' }}
                    <li><a href="{{ simple id='href' name='Link href' }}">
                        {{ simple id='content' name='Link content' }}
                    </a></li>
                {{ /repeatable }}
            </ul>
        </footer>
    </body>
</html>
```


### Available field types:
To mark a place in the template where you want content to be managed in the admin panel, you can use one of the following code snippets:

* `{{ simple }}` - insert simple text that can be edited in admin panel.
  - Available properties: `id`, `name`, `desc`.
  - Example:
    ```
    {{ simple id='slogan' name='Your slogan' desc='Put your motto here' }}
    ```

* `{{ rich }}` - insert text that can be edited in admin panel in WYSIWYG editor. Best used for larger amounts of content.
  - Available properties: `id`, `name`, `desc`.
  - Example:
    ```
    {{ rich id='article' name='Article content' desc='Describe your cat in 1000 words' }}
    ```

* `{{ lang }}` - insert text received from theme lang translations files located in the themes/lang/xx.php directory.
  - Available params: `id`
  - Example:
    ```
    {{ lang id='read_more' }}
    ```

* `{{ repeatable }}...{{ /repeatable }}` - insert multiple groups of fields. This allows you to decide how many blocks of code will be displayed.
  - Available properties: `id`, `name`, `desc`.
  - Example:
    ```
    <ul>
        {{ repeatable id='cat-family' name='Your cat family' }}
        <li>
            <h3>{{ simple id='cat-name' name='Cat name' }}</h3>
            {{ rich id='cat-description' name='Cat description' }}
        </li>
        {{ /repeatable }}
    </ul>
    ```


### Predefined fields available in template files:

* `{{ site_path }}`
* `{{ theme_path }}`
* `{{ lang_code }}`
