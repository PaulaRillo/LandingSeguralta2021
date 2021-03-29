<?php
//  Exit if accessed directly.
defined('ABSPATH') || exit;


//Lazyload Preloader Image
function bravepop_get_preloader(){
   return plugin_dir_url( __FILE__ ) . 'assets/images/preloader.png';
}

/** Define plugin path constant */
if (!defined('BRAVEPOP_PLUGIN_PATH')) {
   define('BRAVEPOP_PLUGIN_PATH', plugin_dir_url(__FILE__));
}


// Enqueue JS and CSS
include __DIR__ . '/lib/helpers/helpers.php';
include __DIR__ . '/lib/enqueue-scripts.php';

// Register Post types
include __DIR__ . '/lib/post-type_popup.php';

//Settings Class
include __DIR__ . '/lib/settings.php';

//Elements Init
include __DIR__ . '/lib/frontend/init.php';

// Init
include __DIR__ . '/lib/init.php';
include __DIR__ . '/lib/stats.php'; //Setup Stats DB
include __DIR__ . '/lib/rest/rest.php'; // Register Custom Rest Api routes
include __DIR__ . '/lib/render.php'; // Render Popup

// Intigrations Init
include __DIR__ . '/lib/integration/init.php';

//Submissions Init
include __DIR__ . '/lib/Submissions.php';
