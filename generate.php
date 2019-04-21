#!/usr/bin/php
<?php
	// Hide warnings
	//error_reporting(E_ERROR | E_PARSE);

	// Composer's autoload
	require_once 'vendor/autoload.php';
	
	// Dynamically load classes from app/classes/*.class.php
	spl_autoload_register(function ($class_name) {
		require_once('app/classes/'.$class_name.'.class.php');
	});

  $prune = false;
  if (count($argv) > 1) {
    if ($argv[1] == '--prune') {
      $prune = true;
    }
  }
  // Load setting
  Settings::read();

  // Read gallery folder
  Reader::read_gallery();

  // Resize images, create thumbnails, and save them in 'publish/gallery'
  ImageHelper::resize_all($prune);

  // Load twig templates
  $loader = new Twig_Loader_Filesystem('source/templates');
  $twig = new Twig_Environment($loader);
  $template = $twig->loadTemplate('page.html.twig');
  
  // Set template variables
  $images = Reader::get_images();
  
  $vars['site'] = [
    'name' => Settings::get_site_name(),
    'description' => Settings::get_site_description(),
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
      'href' => Settings::get_gallery_dir() . $image,
      'thumb' => Settings::get_gallery_dir() . 'thumbs/' . $image,
      'title' => $image_title,
    ];
  }
  
  // Render index.html
  $output = $template->render($vars);
  Filesystem::create_html($output);

  // Copy css, js, and image files in 'publish'
  Filesystem::copy_files();

