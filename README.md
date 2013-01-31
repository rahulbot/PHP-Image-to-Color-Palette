PHP Image to Color Palette
==========================

A quick PHP class to generate a color palette based on an image or a set of images.
This is based on [Sameer Borate's GetCommonColors](http://www.codediesel.com/php/generating-color-palette-from-aimage/).

Testing
-------

Open `index.php` in your browser on a server.

Example
-------

```
require_once("ColorPalette.php");
$localPalette = ColorPalette::GenerateFromLocalImage("MuppetsOfSesameStreet.jpg");
print_r($localPalette);
```

