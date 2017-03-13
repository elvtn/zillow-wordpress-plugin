<?php
   /*
   Plugin Name: Zillow WordPress Plugin
   Plugin URI: https://github.com/elvtn/zillow-wordpress-plugin
   Description: Plugin to display Zillow data using widgets.
   Version: 1.0
   Author: Elvtn, LLC
   Author URI: https://elvtn.com
   License: GPL2
    */

/*
 * Register the Reviews widget.
 */
function elvtn_register_zillow_reviews_widget()
{ 
  register_widget( 'elvtn_Zillow_Reviews' );
}
add_action( 'widgets_init', 'elvtn_register_zillow_reviews_widget' );

?>
