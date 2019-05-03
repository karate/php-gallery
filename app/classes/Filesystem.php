<?php

namespace Gallery;

class Filesystem {

  /**
   *  Creates basic folder structure in 'publish' directory
   * */
  static function create(): void {
    echo "Creating filesystem structure...\n";

    $dirs = [
      Settings::get_export_dir(),
      Settings::get_export_dir() . Settings::get_gallery_dir(),
      Settings::get_export_dir() . Settings::get_gallery_dir() . 'thumbs/',
      Settings::get_export_dir() . Settings::get_resources_dir(),
      Settings::get_export_dir() . Settings::get_vendors_dir(),
    ];

    foreach ($dirs as $dir) {
      self::create_dir($dir);
    }
  }

  /**
   *  Creates a single direcotry if not already there
   */
  static function create_dir($dir_name): void {
    if (!file_exists($dir_name)) {
      try {
        mkdir($dir_name);
      } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
      }
    }
  }


  /**
   *  Creates the index.html file
   */
  static function create_html ($markup): void {
    $fp = fopen(Settings::get_export_dir() . 'index.html', 'w');
    fputs($fp, $markup);
    fclose($fp);
  }


  /**
   * Copies 'source/resources' in the 'publish' directory
   */
  static function copy_files(): void {
    self::copy_directory('source/resources', Settings::get_export_dir() . 'resources');
  }

  /**
   * Recursive function that copies a directory and all it's contents
   */
  private static function copy_directory($src,$dst): void {
    $dir = opendir($src);
    if (!file_exists($dst)) {
      mkdir($dst);
    }
    while(false !== ( $file = readdir($dir)) ) {
      if (( $file != '.' ) && ( $file != '..' )) {
        if ( is_dir($src . '/' . $file) ) {
          self::copy_directory($src . '/' . $file, $dst . '/' . $file);
        }
        else {
          copy($src . '/' . $file, $dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

  static function delete_dir($dir): void {
    if (file_exists($dir)) {
      $contents = glob($dir . '*', GLOB_MARK);
      foreach ($contents as $file) {
          if (is_dir($file)) {
              self::delete_dir($file);
          } else {
              unlink($file);
          }
      }
      rmdir($dir);
    }
  }
}
