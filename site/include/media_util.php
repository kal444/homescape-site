<?php

require_once('db.php');

if (!isset($db)) {
  $db=new DB;
}

// set the umask to user/group read/writable
umask(0007);

// These will be populated from the database's application_default table
$incoming_dir="/home/kal/homescape/incoming/";
$media_dir="/home/kal/homescape/media/";
$suffix_separator='____';
$media_sizes = array (
  'thumbnail' => array (
    'width' => 180,
    'height' => 180,
    'bg_red' => 128,
    'bg_green' => 128,
    'bg_blue' => 128,
    'timeout' => 600,
    'quality' => 75,
    'suffix' => "thumbnail"
  ),
  'intermediate' => array (
    'width' => 600,
    'height' => 600,
    'bg_red' => 128,
    'bg_green' => 128,
    'bg_blue' => 128,
    'timeout' => 600,
    'quality' => 75,
    'suffix' => "intermediate"
  )
);

class Media_Util {
  // This function will split out the file's name and extension parts
  function get_name_ext ($filename) {
    $last_period = strrpos($filename, ".");
    if ($last_period === false) {
      $last_period = strlen($filename);
    }
    $file_noext = substr($filename, 0, $last_period);
    $file_ext = substr($filename, $last_period, strlen($filename));

    return array($file_noext, $file_ext);
  }

  // This function will generate the list of media to add as an array
  function scan_media () {
    global $incoming_dir;
    global $suffix_separator;

    $result = array();

    if (is_dir($incoming_dir)) {
      if ($idh = opendir($incoming_dir)) {
        while (($entry = readdir($idh)) !== false) {
          if (is_file($incoming_dir . $entry)) {
            $entry_lower = strtolower($entry);
            # keep only lowercase names
            rename($incoming_dir . $entry, $incoming_dir . $entry_lower);
            // if there are any files with the suffix separator in it, assume that we created it and skip it
            if (!preg_match("/$suffix_separator/", $entry_lower)) {
              $result[] = $incoming_dir . $entry_lower;
            }
          }
        }
        closedir($idh);
      }
    }

    return $result;
  }

  /* the main function used to create perspectively correct images
     the returned array is an associative array with a key of files in the input array
     the values are themselves an associative array with the following keys:
       scaled - scaled filename
       type - textual type of the original
       width
       height


    one idea for future is to turn off timeout completely on this part of the site. Then have then
    script run to completion with a lock file in place. Subsequent tries to use "scan&add" will show
    that there is a process already working on the file in incoming and advise the user to wait with 
    a progress report
  */
  function build_media_scaled ($files, $size) {
    global $suffix_separator;
    global $media_sizes;

    if (!array_key_exists($size, $media_sizes)) {
      return;
    }

    $media_size = $media_sizes[$size];

    // set a higher timeout value
    set_time_limit($media_size['timeout']);

    $result = array();

    foreach($files as $file) {
      switch (exif_imagetype($file)) {
      case IMAGETYPE_GIF:
        $oimg = imagecreatefromgif($file) or die("Cannot initialize new original image stream");
        $media_text = "GIF";
        break;
      case IMAGETYPE_JPEG:
        $oimg = imagecreatefromjpeg($file) or die("Cannot initialize new original image stream");
        $media_text = "JPEG";
        break;
      case IMAGETYPE_PNG:
        $oimg = imagecreatefrompng($file) or die("Cannot initialize new original image stream");
        $media_text = "PNG";
        break;
      case IMAGETYPE_SWF:
        $oimg=false;
        $media_text = "FLASH";
        break;
      default:
        $oimg=false;
        $media_text = "Others";
      }

      list($file_noext, $file_ext) = Media_Util::get_name_ext($file);

      $filename = $file_noext . $suffix_separator . $media_size['suffix'] . ".jpg";

      if (file_exists($filename)) {
        list($media_width, $media_height, , ) = getimagesize($filename);
        $result[$file] = array(
          'original'  =>  $file,
          'scaled'    =>  $filename, 
          'type'      =>  $media_text,
          'width'     =>  $media_width,
          'height'    =>  $media_height
        );
        continue;
      }

      if ($oimg) {
        // save the size of the original for later
        $ox = imagesx($oimg);
        $oy = imagesy($oimg);
      } else {
        // if it's not an image, default it to default sizes
        $ox = $media_size['width'];
        $oy = $media_size['height'];
      }

      if ($ox >= $oy) {
        $dimg = imagecreatetruecolor($media_size['width'], 
                                     $media_size['height']) 
          or die("Cannot initialize new image stream");
        $dx = $media_size['width'];
        $dy = $media_size['height'];
      } else {
        $dimg = imagecreatetruecolor($media_size['height'], 
                                     $media_size['width']) 
          or die("Cannot initialize new image stream");
        $dx = $media_size['height'];
        $dy = $media_size['width'];
      }

      $dimg_bgcolor = imagecolorallocate($dimg, $media_size['bg_red'], 
                                                $media_size['bg_green'], 
                                                $media_size['bg_blue']);
      imagefill($dimg, 0, 0, $dimg_bgcolor);

      if ($oimg) {
        // need to perform perspective calculations here
        $xratio = $ox/$dx;
        $yratio = $oy/$dy;

        if ($xratio >= $yratio) {
          $ratio = $xratio;
        } else {
          $ratio = $yratio;
        }

        $dx_real = $ox/$ratio;
        $dy_real = $oy/$ratio;
        $dx_orig = ($dx - $dx_real)/2;
        $dy_orig = ($dy - $dy_real)/2;

        imagecopyresampled($dimg, $oimg, $dx_orig, $dy_orig, 0, 0, $dx_real, $dy_real, $ox-1, $oy-1);
        imagedestroy($oimg);
      } else {
        // create some text for the flash/others images types' scaled images here
      }

      imagejpeg($dimg, $filename, $media_size['quality']);

      imagedestroy($dimg);

      $result[$file] = array(
        'original'  =>  $file,
        'scaled'    =>  $filename, 
        'type'      =>  $media_text,
        'width'     =>  $dx,
        'height'    =>  $dy
      );
    }

    return $result;
  }

  function build_media_thumbnails ($files) {
    # creates the thumbnails here...
    # then, return the list of the thumbnails created. That list can be used as argument to 
    # media.php?file=blahblah
    # the ones that are not images, just create a blank thumbnail
    return Media_Util::build_media_scaled($files, 'thumbnail');
  }

  function build_media_intermediates ($files) {
    # creates the normail sized pictures here...
    # then, return the list of the intermediates created. That list can be used as argument to 
    # the ones that are not images, just create a blank intermediate
    return Media_Util::build_media_scaled($files, 'intermediate');
  }

  function build_media_lib ($files) {
    # moves and creates the images in the right directory.
    # we shouldn't need the original file here...
    # also does the Right Thing (R) in DB
    # Where to create items? here?

    global $db;
    global $media_dir;
    global $suffix_separator;
    global $media_sizes;

    $result = array();

    $subdir = date("YmdHis") . '/';
    $dir = $media_dir . $subdir;

    foreach ($files as $file) {
      list($file_noext, $file_ext) = Media_Util::get_name_ext($file);

      $tb_filename = $file_noext . $suffix_separator . $media_sizes['thumbnail']['suffix'] . ".jpg";
      $im_filename = $file_noext . $suffix_separator . $media_sizes['intermediate']['suffix'] . ".jpg";

      if (!file_exists($file)) {
        trigger_error("Can't find original image for $file", E_USER_ERROR);
      }

      if (!file_exists($tb_filename)) {
        trigger_error("Can't find thumbnail image for $file", E_USER_ERROR);
      }

      if (!file_exists($im_filename)) {
        trigger_error("Can't find intermediate image for $file", E_USER_ERROR);
      }

      $action[] = array(
        'type'     => image_type_to_mime_type(exif_imagetype($file)),
        'old_fn'   => $file,
        'old_tbfn' => $tb_filename,
        'old_imfn' => $im_filename,
        'new_fn'   => $dir . basename($file),
        'new_tbfn' => $dir . basename($tb_filename),
        'new_imfn' => $dir . basename($im_filename),
        'db_fn'    => $subdir . basename($file),
        'db_tbfn'  => $subdir . basename($tb_filename),
        'db_imfn'  => $subdir . basename($im_filename)
      );
    }

    if (is_array($action)) {
      mkdir($dir, 0777);

      foreach ($action as $medium) {
        rename($medium['old_fn'],   $medium['new_fn']);
        rename($medium['old_tbfn'], $medium['new_tbfn']);
        rename($medium['old_imfn'], $medium['new_imfn']);

        $db->query("
          INSERT INTO media 
          (media_loc, media_thumbnail_loc, media_intermediate_loc, media_type) 
          values 
          ('{$medium['db_fn']}', '{$medium['db_tbfn']}', '{$medium['db_imfn']}', '{$medium['type']}')
        ");

        $result[$medium['old_fn']] = mysql_insert_id($db->link);
      }
    }

    return $result;
  }
}

?>
