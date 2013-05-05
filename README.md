php-jcktraker
=============

Simple debug bar for PHP as standalone file (Make for my students). 

Installation
------------

use `require_once` for include JckTraker.php file in your project.

Using traker (procedural)
-------------------------

`JckTraker` allow some functions for debug your PHP scripts : 
 - `debug( mixedvar $var )` : As var_dump
 - `info( string $message )` : A notice
 - `error( string $message )` : An error
 - `warning( string $message )` : A warning
 - `success( string $message )` : Congatulation :P
 - `database( string $message )` : Data access

Using class
-----------

Previous functions call a static methods in JckTraker class, with the same name.

Example
-------

```php
<?php
require_once 'lib/JckTraker.php';

// Now, JckTraker is ready
debug($_SERVER); // a var_dump like
info("Jcktraker is enabled");
?>
```

JckTraker debug bar is on bottom right corner. Roll on it for play with options.

