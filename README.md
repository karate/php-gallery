# Installation
- `git clone https://github.com/karate/php-gallery.git`
- `composer install`
- Edit `site` options in `app/settings.yml`.
- Place all your .jpg/.png files in `source/gallery`.
  - The filenames will become the image titles, so name your files accordingly.
  - Underscores in filenames will be replaced by spaces.
  - Begin your filename with a number, followed by a dash ('-'), to define image ordering. It will not be included in the image title.
- Run `./generate.php` to generate static site.
- Run `./generate.php --prune` to delete existing images and force regeneration.
- The generated site is in directory `publish`, upload it on a web server.
