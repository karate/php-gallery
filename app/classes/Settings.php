<?php
namespace Gallery;

use Symfony\Component\Yaml\Yaml;
	
class Settings {
	private static $settings;

  /**
   * Parse the app/settings.yml file
   */
	static function read(): void {
		echo "Reading settings...\n";
		$settings = Yaml::parse(file_get_contents('app/settings.yml'));

    // Add trailing slash
    foreach ($settings['paths'] as &$path) {
      $path = rtrim($path, '/') . '/';
    }

    self::$settings = $settings;
	}

  /**
   * Getters FTW!
   */
	static function get_all(): array {
		return self::$settings;
	}

	static function get_site_name(): string {
		return self::$settings['site']['name'];
	}

	static function get_site_description(): string {
		return self::$settings['site']['description'];
	}

  static function get_image_width(): int {
    return self::$settings['site']['images']['max_width'];
  }

  static function get_thumb_width(): int {
    return self::$settings['site']['images']['thumb_width'];
  }

	static function get_image_source_dir(): string {
		return self::$settings['paths']['image_source_dir'];
	}

  static function get_export_dir(): string {
    return self::$settings['paths']['export_dir'];
  }

  static function get_gallery_dir(): string {
    return self::$settings['paths']['gallery_dir'];
  }

  static function get_resources_dir(): string {
    return self::$settings['paths']['resources_dir'];
  }

  static function get_vendors_dir(): string {
    return self::$settings['paths']['vendors_dir'];
  }
}
