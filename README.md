Config
======
[![Latest Stable Version](https://poser.pugx.org/luracast/config/v/stable.png)](https://packagist.org/packages/luracast/config)
[![Total Downloads](https://poser.pugx.org/luracast/config/downloads.png)](https://packagist.org/packages/luracast/config)
[![Latest Unstable Version](https://poser.pugx.org/luracast/config/v/unstable.png)](https://packagist.org/packages/luracast/config)
[![License](https://poser.pugx.org/luracast/config/license.png)](https://packagist.org/packages/luracast/config)
[![Build Status](https://travis-ci.org/Luracast/Config.svg?branch=master)](https://travis-ci.org/Luracast/Config)

Config class for loading configuration arrays from various files and provide easy access to nested properties with dot syntax

Lazy loads configuration information when requested using `Config::get('file.property')` or `$config['file.property']`

For example :-

    Config::get['database.connections.sqlite']

will load `database.php` which returns an array that contains connections property which contains the sqlite property
value of which will be returned. `$path` given in the constructor is the path it will look for the file.

When an environment string is specified, it will look for a folder with that name inside the path and use the
returned array to override the properties is original config file thus allowing some customization