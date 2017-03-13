<?php
/**
 * Plugin Name:   Zillow Reviews Widget
 * Plugin URI:    https://github.com/elvtn/zillow-wordpress-plugin
 * Description:   Adds a Zillow Reviews widget.
 * Version:       1.0
 * Author:        Elvtn, LLC
 * Author URI:    https://elvtn.com
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'zillow_api.php' );

class elvtn_Zillow_Reviews extends WP_Widget {


  // Set up the widget name and description.
  public function __construct() {
    $widget_options = array( 'classname' => 'elvtn_Zillow_Reviews', 'description' => 'Zillow Reviews' );
    parent::__construct( 'elvtn_Zillow_Reviews', 'Zillow Reviews', $widget_options );
  }


  // Create the widget output.
  public function widget( $args, $instance ) {
    $title   = apply_filters( 'widget_title', $instance[ 'title'   ] );
    $zwsid   = apply_filters( 'widget_title', $instance[ 'zwsid'   ] );
    $scrname = apply_filters( 'widget_title', $instance[ 'scrname' ] );
    $email   = apply_filters( 'widget_title', $instance[ 'email'   ] );
    $count   = apply_filters( 'widget_title', $instance[ 'count'   ] );
    $members = apply_filters( 'widget_title', $instance[ 'members' ] );

    echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
    
    // Call Zillow API
    $zillow_api = new Zillow_Api($zwsid);

    $reviews = $zillow_api->GetProReviews(array('screenname' => 'Denverlender', 'count' => $count)); ?>

    <?php echo $reviews ?>
    
    <?php echo $args['after_widget'];
  }

  
  // Create the admin area widget settings form.
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
    $zwsid = ! empty( $instance['title'] ) ? $instance['zwsid'] : '';
    $scrname = ! empty( $instance['title'] ) ? $instance['scrname'] : '';
    $email = ! empty( $instance['title'] ) ? $instance['email'] : '';
    $count = ! empty( $instance['count'] ) ? $instance['count'] : '';
    $members = ! empty( $instance['title'] ) ? $instance['members'] : ''; ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'zwsid' ); ?>">ZWS ID:</label>
      <input type="text" id="<?php echo $this->get_field_id( 'zwsid' ); ?>" name="<?php echo $this->get_field_name( 'zwsid' ); ?>" value="<?php echo esc_attr( $zwsid ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'count' ); ?>">Number of reviews (3-10):</label>
      <input type="text" size="2" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $count ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'count' ); ?>">Screen Name:</label>
      <input type="text" size="2" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $count ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'count' ); ?>">Number of reviews (3-10):</label>
      <input type="text" size="2" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $count ); ?>" />
 

   </p><?php
  }


  // Apply settings to the widget instance.
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance[ 'title' ]   = strip_tags( $new_instance[ 'title' ] );
    $instance[ 'zwsid' ]   = strip_tags( $new_instance[ 'zwsid' ] );
    $instance[ 'count' ]   = strip_tags( $new_instance[ 'count' ] );
    $instance[ 'scrname' ] = strip_tags( $new_instance[ 'scrname' ] );
    $instance[ 'email' ]   = strip_tags( $new_instance[ 'email' ] );
    $instance[ 'members' ] = strip_tags( $new_instance[ 'members' ] );
    return $instance;
  }

}

?>
