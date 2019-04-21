<?php

class ImageHelper {
  private static $max_width;
  private static $gallery_dir;

    /**
     * Resize all images from the image source directory, create thumbnails, and save them
     * in the 'publish' directory
     */
    static function resize_all($prune = false) {
      if ($prune) {
        echo "Deleting old gallery...\n";
        Filesystem::delete_dir(Settings::get_export_dir() . Settings::get_gallery_dir());
      }
      
      // Create filesystem
      Filesystem::create();
      
      echo "Resizing images...\n";
      $images = Reader::get_images();
      self::$gallery_dir = Settings::get_export_dir() . Settings::get_gallery_dir();

      $progressBar = new \ProgressBar\Manager(0, count($images));
      $progressBar->setFormat('%current%/%max% [%bar%] %percent%%');

      foreach ($images as $idx => $filename) {
        if (!file_exists(self::$gallery_dir . $filename)) {
          $progressBar->advance();
          self::resize_image($filename, 'default');
        }
        if (!file_exists(self::$gallery_dir . 'thumbs/' .  $filename)) {
          self::resize_image($filename, 'thumb');
        }
      }
    }

    /**
     * Resize a single image and create a thumbnail
     */
    private static function resize_image($filename, $type = 'default') {
      $full_path_file = Settings::get_image_source_dir() . $filename;			

      $info = getimagesize($full_path_file);
      $mime = $info['mime'];
      
      if (!in_array($mime, ['image/jpeg', 'image/png'])) {
        throw new Exception("Unknown image type: $filename\n");
        
      }

      if ($type == 'thumb') {
        $filename = self::$gallery_dir . 'thumbs/' . $filename;
        $max_width = Settings::get_thumb_width();			
      }
      else {
        $filename = self::$gallery_dir . $filename;
        $max_width = Settings::get_image_width();			
      }

      $imagesize = getimagesize($full_path_file);
      $src_width = $imagesize[0];
      $src_height = $imagesize[1];

      $dst_h_multiplier = $max_width / $src_width;
      $dst_h = $dst_h_multiplier * $src_height;

      $dst = imagecreatetruecolor($max_width, $dst_h);

      if ($mime == 'image/jpeg') {
        $src = imagecreatefromjpeg($full_path_file);
      }
      elseif ($mime == 'image/png') {
        $src = imagecreatefrompng($full_path_file);
        // This is needed to ensure transparency is preserved. i don't know...
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
      }

      imagecopyresampled($dst, $src, 0, 0, 0, 0, $max_width, $dst_h ,$src_width, $src_height);

      if ($mime == 'image/jpeg') {
        imagejpeg($dst, $filename ,100);
      }
      elseif ($mime == 'image/png') {
        imagepng($dst, $filename ,9);
      }
    }
  }
