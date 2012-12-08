<?php

$file = $_GET['file'];

switch (exif_imagetype($file)) {
case IMAGETYPE_GIF:
  $img = imagecreatefromgif($file) or die("Cannot initialize new image stream");
  header("Content-type: image/gif");
  imagegif($img);
  imagedestroy($fimg);
  break;
case IMAGETYPE_JPEG:
  $img = imagecreatefromjpeg($file) or die("Cannot initialize new image stream");
  header("Content-type: image/jpeg");
  imagejpeg($img);
  imagedestroy($fimg);
  break;
case IMAGETYPE_PNG:
  $img = imagecreatefrompng($file) or die("Cannot initialize new image stream");
  header("Content-type: image/png");
  imagepng($img);
  imagedestroy($fimg);
  break;
case IMAGETYPE_SWF:
  $swf = fopen($file, 'rb');
  header("Content-type: application/x-shockwave-flash");
  header("Content-Length: " . filesize($file));
  fpassthru($swf);
  break;
}


?>
