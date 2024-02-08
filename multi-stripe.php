<?php

/*
Plugin Name: Multiple Stripes
Plugin URI: https://codepixelzmedia.com.np/
Description: This is plugin for multiple stripe integration.
Version: 1.0.0
Author: CPM
Author URI: https://codepixelzmedia.com.np/

*/

if (!defined('ABSPATH')) {
        exit;
};
/* require plugin loder file */
$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "multi-stripe" . DIRECTORY_SEPARATOR  . "multi-stripe-loader.php";
require $init_file;