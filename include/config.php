<?php
// Show footer
define("showFooter", true);

// Gallery thumbnails size
define("thumbSize", 100);

// Image preview size
define("imageSize", 800);

// Path to the gallery, NEEDED for cron thumbnail creation
define("absolutePath", '');

/* Warning: if you install the gallery with cron job
 * thumbnail creation, be carefull when changing the
 * thumb sizes, this will create new thumbs for all
 * images, can be a lot ;-)
 */
?>