## About Wiloke CLI
Wiloke CLI is a PHP-CLI tool that helps you easily setup phpunit and commonly used components

## Installation

To install Wiloke CLI, please run the following command line:
<pre style="background: black; color: white">
composer require wilokecom/phpcli
</pre>

## Setting up PHPUnit Test for WordPress

EXAMPLES

<pre style="background: black; color: white">
# Generate PHPUnit Test inside a plugin
./vendor/bin/wilokecli make:unittest plugins sample-plugin

# Generate PHPUnit Test inside a theme
./vendor/bin/wilokecli make:unittest themes sample-theme
</pre>

SUBCOMMANDS

<ul>
    <li>homeurl: Enter in your website url</li>
    <li>rb: Rest Base. EG: wiloke/v2</li>
    <li>namespace: Enter in your Unit Test Namespace. You can define your Unit Test Namespace under composer.json. 
EG: WilokeTests (1)</li>
    <li>authpass: This feature is available since WordPress 5.6. To create your Application Password: Log into your 
site with your administrator account -> Profile -> My Profile -> Create an Application Password
</li>
    <li>admin_username: The username of your administrator account.</li>
</ul>

EXAMPLES With SUBCOMMANDS
<pre style="background: black; color: white">
./vendor/bin/wilokecli make:unittest plugins sample-plugin --homeurl=https://wiloke.com --rb=wiloke/v2
</pre>


(1): Define Unit Test namespace
<pre style="background: black; color: white">
{
    "autoload": {
        "psr-4": {
          "WilokeTests\\": "tests/"
        }
    },
}
</pre>
