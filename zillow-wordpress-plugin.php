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

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'elvtn_zillow_api.php' );

class elvtn_Zillow_Reviews extends WP_Widget
{
  // Set up the widget name and description.
  public function __construct() {
    $widget_options = array( 'classname' => 'elvtn_Zillow_Reviews', 'description' => 'Zillow Reviews' );
    parent::__construct( 'elvtn_Zillow_Reviews', 'Zillow Reviews', $widget_options );
  }

  // Create the widget output.
  public function widget( $args, $instance ) {
    $title     = apply_filters( 'widget_title', $instance[ 'title'     ] );
    $partnerId = apply_filters( 'widget_title', $instance[ 'partnerId' ] );
    $user      = apply_filters( 'widget_title', $instance[ 'user'      ] );
    $count     = intval(apply_filters( 'widget_title', $instance[ 'count'     ] ) );
    
    echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];

    // See if this is already saved in a transient to avoid calling again
    $transient_key = 'elvtn_zillow_reviews_widget_' . $this->id;
    if ( FALSE == ( $reviews = get_transient( $transient_key ) ) )
    {
      // Call Zillow API
      $zillow_api = new Elvtn_Zillow_Api($partnerId);

      $reviews = $zillow_api->getPublishedLenderReviews($user, intval($count));

      // Save result for a day to avoid excessive API calls to Zillow
      set_transient( $transient_key, $reviews , 60 * 60 * 24 );
    }
    
    echo $reviews;
    
    echo $args['after_widget'];
  }

  
  // Create the admin area widget settings form.
  public function form( $instance ) {
    $title     = ! empty( $instance['title'] )     ? $instance['title']     : '';
    $partnerId = ! empty( $instance['partnerId'] ) ? $instance['partnerId'] : '';
    $user      = ! empty( $instance['user']  )     ? $instance['user']      : '';
    $count     = ! empty( $instance['count'] )     ? intval($instance['count'])     : 3; ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br/>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'partnerId' ); ?>">Partner ID:</label><br/>
      <input type="text" id="<?php echo $this->get_field_id( 'partnerId' ); ?>" name="<?php echo $this->get_field_name( 'partnerId' ); ?>" value="<?php echo esc_attr( $partnerId ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'user' ); ?>">Zillow Screen Name:</label><br/>
      <input type="text" id="<?php echo $this->get_field_id( 'user' ); ?>" name="<?php echo $this->get_field_name( 'user' ); ?>" value="<?php echo esc_attr( $user ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'count' ); ?>">Number of Reviews:</label><br/>
      <input type="text" size="2" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $count ); ?>" />
    
      </p><?php
  }


  // Apply settings to the widget instance.
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance[ 'title' ]     = strip_tags( $new_instance[ 'title'     ] );
    $instance[ 'partnerId' ] = strip_tags( $new_instance[ 'partnerId' ] );
    $instance[ 'count' ]     = strip_tags( $new_instance[ 'count'     ] );
    $instance[ 'user' ]      = strip_tags( $new_instance[ 'user'      ] );
    return $instance;
  }

}

// Register the Reviews widget.
function elvtn_register_zillow_reviews_widget()
{ 
  register_widget( 'elvtn_Zillow_Reviews' );
}
add_action( 'widgets_init', 'elvtn_register_zillow_reviews_widget' );

?>

