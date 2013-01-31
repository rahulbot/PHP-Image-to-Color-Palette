<?php
require_once("ColorPalette.php");
?>

<html>

  <head>
    <title>PHP Image to Color Palette Examples</title>
  </head>

<body>

<h1>PHP Image to Color Palette Examples</h1>

<h2>Local Image</h2>
<?php
$localPalette = ColorPalette::GenerateFromLocalImage("MuppetsOfSesameStreet.jpg");
?>
<img src="MuppetsOfSesameStreet.jpg" width="300px">
<?php
printPalette($localPalette);
?>

<h2>Remote Image</h2>
<?php
$url = "http://upload.wikimedia.org/wikipedia/en/d/dd/Muppets_take_manhattan.jpg";
$remotePalette = ColorPalette::GenerateFromUrl($url);
?>
<img src="<?php print $url?>" height="150px">
<?php
printPalette($remotePalette);
?>

<h2>Remote Set of Images</h2>
<?php
$urls = array(
  "http://images1.wikia.nocookie.net/__cb20110228193806/muppet/images/thumb/1/12/Muppetmovieposter.jpg/200px-Muppetmovieposter.jpg",
  "http://images3.wikia.nocookie.net/__cb20090709025947/muppet/images/thumb/c/ca/GMCposter.jpg/200px-GMCposter.jpg",
  "http://images2.wikia.nocookie.net/__cb20090709030020/muppet/images/thumb/a/a4/Poster.manhattan.jpg/200px-Poster.manhattan.jpg",
  "http://images3.wikia.nocookie.net/__cb20090709014824/muppet/images/thumb/b/be/Mxcposter.jpg/200px-Mxcposter.jpg",
  "http://images2.wikia.nocookie.net/__cb20090709030057/muppet/images/thumb/7/74/Mtiposter.jpg/200px-Mtiposter.jpg",
  "http://images3.wikia.nocookie.net/__cb20090709030127/muppet/images/thumb/b/bc/Mfsposter.jpg/200px-Mfsposter.jpg",
  "http://images1.wikia.nocookie.net/__cb20060125061949/muppet/images/thumb/5/56/IAVMMCMDVD.JPG/200px-IAVMMCMDVD.JPG",
  "http://images2.wikia.nocookie.net/__cb20060318061146/muppet/images/thumb/9/9d/Ozposter.jpg/200px-Ozposter.jpg",
  "http://images3.wikia.nocookie.net/__cb20110727202911/muppet/images/thumb/9/99/TheMuppets1Sheet.jpg/200px-TheMuppets1Sheet.jpg",
);
$remotePalette = ColorPalette::GenerateFromUrls($urls);
foreach($urls as $url){
?>
  <img src="<?php print $url?>" height="150px">
<?php
}
?>
<br />
<?php
printPalette($remotePalette);
?>

</body>

</html>

<?php
function printPalette($palette){
  foreach(array_slice(array_keys($palette),0,10) as $color){
?>  <div style="background-color:<?php print $color?>;display:inline-block;width:50px;height:50px;"><?=$color?></div>
<?php
  }
}
?>