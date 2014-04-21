## A utility class to help extract TROPHY.TRP files.

This main extraction code is a port from TROPHY.TRP Extractor by Red Squirrel
(http://www.psp-cheats.it/redsquirrel)

```php
require_once 'TRPExtractor.class.php';

$trpex = new TRPExtractor();

if ($trpex->extract()) {
     echo "Success!";
} else {
     echo "Failed!";
}
```

or run the test.php file.

```
php test.php
```