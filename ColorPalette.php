<?php

/**
 * This class can be used to get the most common colors in an image, or to generate
 * a color palette from a set of images.  It handles a simple local cache of images.
 * @see Copied and modified from http://www.codediesel.com/php/generating-color-palette-from-aimage/
 */
class ColorPalette {

  /**
   * It is important to cluster the colors into groups, otherwise you won't find any duplicates in
   * photos.  Without rounding you would end up with a lot of colors that are ALMOST the same.
   * Using 0x33 here reduces this to the "web safe" color palette.
   */
  const COLOR_ROUNDING_COEFF = 0x33;

  /**
   * Helper method that caches files locally to the system temp dir
   */
  static private function CacheFromUrl($imageUrl,$useCache=true){
    $localFile = sys_get_temp_dir()."/".md5($imageUrl);
    if(file_exists($localFile) && $useCache){
      return $localFile;
    }
    $ch = curl_init();
    $worked = true;
    curl_setopt($ch, CURLOPT_URL, $imageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $data = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($http_status==200) {
      file_put_contents($localFile, $data);
    } else {
      $worked = false;
    }
    curl_close($ch);
    return $worked ? $localFile : null;
  }

  /**
   * Generate a color palette from a single online image
   */
  static public function GenerateFromUrl($imageUrl,$useCache=true) {
    $localFile = ColorPalette::CacheFromUrl($imageUrl,$useCache);
    if($localFile!=null) {
      return ColorPalette::GenerateFromLocalImage($localFile);
    } 
    return array();  // fails silently
  }

  /**
   * Generate a single color palette from a list of online image urls
   */
  static public function GenerateFromUrls($imageUrlList, $useCache=true){
     $colorHexToFreq = array();
    foreach($imageUrlList as $imageUrl){
      $colors = ColorPalette::GenerateFromUrl($imageUrl,$useCache);
      //$colors = array_slice($colors,0,10,true);  // not sure if we should trim to the top 10 and then merge?
      foreach($colors as $color=>$freq){
        if( !array_key_exists($color,$colorHexToFreq) ){
          $colorHexToFreq[$color] = 0;
        }
        $colorHexToFreq[$color] += $freq;
      }
    }
    arsort($colorHexToFreq);
    return $colorHexToFreq;
  }

  /**
   * Returns the colors of the image in an array, ordered in descending order, where
   * the keys are the colors, and the values are the count of the color.
   * @param $filePath   the path to the local image file
   * @return an array keyed by hex color value, mapping to the frequency of use in the images
   */
  static public function GenerateFromLocalImage($filePath) {
    if( !file_exists($filePath)) {
      throw new InvalidArgumentException("File not found (".$filePath.")");
    }

    // resize the image for a reasonable amount of colors
    $PREVIEW_WIDTH    = 150;
    $PREVIEW_HEIGHT   = 150;
    $size = GetImageSize($filePath);
    $scale=1;
    if ($size[0]>0){
      $scale = min($PREVIEW_WIDTH/$size[0], $PREVIEW_HEIGHT/$size[1]);
    }
    if ($scale < 1) {
      $width = floor($scale*$size[0]);
      $height = floor($scale*$size[1]);
    } else {
      $width = $size[0];
      $height = $size[1];
    }
    $image_resized = imagecreatetruecolor($width, $height);
    if ($size[2]==IMAGETYPE_GIF) {
      $image_orig=imagecreatefromgif($filePath);
    }
    if ($size[2]==IMAGETYPE_JPEG) {
      $image_orig=imagecreatefromjpeg($filePath);
    }
    if ($size[2]==IMAGETYPE_PNG) {
      $image_orig=imagecreatefrompng($filePath);
    }
    //WE NEED NEAREST NEIGHBOR RESIZING, BECAUSE IT DOESN'T ALTER THE COLORS
    imagecopyresampled($image_resized, $image_orig, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
    $im = $image_resized;
    $imgWidth = imagesx($im);
    $imgHeight = imagesy($im);

    // walk the image counting colors
    for ($y=0; $y < $imgHeight; $y++) {
      for ($x=0; $x < $imgWidth; $x++) {
        $index = imagecolorat($im,$x,$y);
        $Colors = imagecolorsforindex($im,$index);
        $div = ColorPalette::COLOR_ROUNDING_COEFF;
        $Colors['red'] = round(round(($Colors['red'] / $div)) * $div);
        $Colors['green'] = round(round(($Colors['green'] / $div)) * $div);
        $Colors['blue'] = round(round(($Colors['blue'] / $div)) * $div);
        $hexarray[]=substr("0".dechex($Colors['red']),-2).substr("0".dechex($Colors['green']),-2).substr("0".dechex($Colors['blue']),-2);
      }
    }
    $hexarray=array_count_values($hexarray);
    natsort($hexarray);
    $hexarray=array_reverse($hexarray,true);
    return $hexarray;
  }

}
?>