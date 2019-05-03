#!/usr/bin/php
<?php
  // Hide warnings
  //error_reporting(E_ERROR | E_PARSE);

  // Declare strict type
  declare(strict_types=1);

  // Composer's autoload
  require_once 'vendor/autoload.php';

  $prune = false;
  if (count($argv) > 1) {
    if ($argv[1] == '--prune') {
      $prune = true;
    }
  }
  // Load setting
  Gallery\Settings::read();

  // Read gallery folder
  Gallery\Reader::read_gallery();

  // Resize images, create thumbnails, and save them in 'publish/gallery'
  Gallery\ImageHelper::resize_all($prune);

  // Load twig templates
  $loader = new Twig_Loader_Filesystem('source/templates');
  $twig = new Twig_Environment($loader);
  $template = $twig->loadTemplate('page.html.twig');

  // Set template variables
  $images = Gallery\Reader::get_images();

  $vars['site'] = [
    'name' => Gallery\Settings::get_site_name(),
    'description' => Gallery\Settings::get_site_description(),
  ];

  // Create image properties for yaml
  foreach ($images as $image) {
    // Remove leading number and dash from filename
    // (used only for ordering)
    $image_title = preg_replace("/^[0-9]*-/", "", $image);
    // Remove file extension
    $image_title = preg_replace("/\.[a-zA-Z]{1,4}$/", "", $image_title);
    // Replace underscores with spaces
    $image_title = str_replace("_", " ", $image_title);

    $vars['content'][] = [
      'href' => Gallery\Settings::get_gallery_dir() . $image,
      'thumb' => Gallery\Settings::get_gallery_dir() . 'thumbs/' . $image,
      'title' => $image_title,
    ];
  }

  // Render index.html
  $output = $template->render($vars);
  Gallery\Filesystem::create_html($output);

  // Copy css, js, and image files in 'publish'
  Gallery\Filesystem::copy_files();

