<?php

namespace Gallery;

class Reader {
  private static $images = [];

   /**
    * Read the 'gallery' directory and get all .jpg files
    */
  static function read_gallery(): void {
    echo "Reading gallery...\n";
    $directory = Settings::get_image_source_dir();
    $images = glob($directory . "*");
    foreach ($images as &$image) {
      $image = str_replace($directory, '', $image);
    }
    self::$images = $images;
  }

  static function get_images(): array {
    return self::$images;
  }
}
