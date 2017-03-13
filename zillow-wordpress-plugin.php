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
    $user    = apply_filters( 'widget_title', $instance[ 'user'    ] );
    $count   = apply_filters( 'widget_title', $instance[ 'count'   ] );
    $members = apply_filters( 'widget_title', $instance[ 'members' ] );

    // Make sure count is a valid value (integer range 3-10, inclusive)
    if( is_numeric( $count ) )
    {
       $i = intval( $count );
       if($i < 3) { $i = 3; }
       elseif($i > 10) { $i = 10; }
       $count = $i;
       $instance[ 'count' ] = $i;
    }
    else
    {
       $count = 3;
       $instance[ 'count' ] = 3;
    }

    
    echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];


    // See if this is already saved in a transient to avoid calling again
    $transient_key = 'elvtn_zillow_reviews_widget_' . $this->id;
    if ( FALSE == ( $reviews = get_transient( $transient_key ) ) )
    {
       // Call Zillow API
       $zillow_api = new Zillow_Api($zwsid);

       // Determine if we have a screen name or email based on having an @ symbol
       if( strpos($user, '@') == FALSE )
       {
          $reviews = $zillow_api->GetProReviews(array('screenname' => $user, 'count' => $count, 'members' => $members));
       }
       else
       {
          $reviews = $zillow_api->GetProReviews(array('email' => $user, 'count' => $count, 'members' => $members));
       }

       set_transient( $transient_key, $reviews , 60 * 60 * 24 );
    }
    
     ?>

    <?php echo $reviews ?>
    
    <?php echo $args['after_widget'];
  }

  
  // Create the admin area widget settings form.
  public function form( $instance ) {
    $title   = ! empty( $instance['title'] )   ? $instance['title']   : '';
    $zwsid   = ! empty( $instance['zwsid'] )   ? $instance['zwsid']   : '';
    $user    = ! empty( $instance['user']  )   ? $instance['user']    : '';
    $count   = ! empty( $instance['count'] )   ? $instance['count']   : '';
    $members = ! empty( $instance['members'] ) ? $instance['members'] : ''; ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br/>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'zwsid' ); ?>">ZWS ID:</label><br/>
      <input type="text" id="<?php echo $this->get_field_id( 'zwsid' ); ?>" name="<?php echo $this->get_field_name( 'zwsid' ); ?>" value="<?php echo esc_attr( $zwsid ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'user' ); ?>">Zillow Screen Name or E-Mail:</label><br/>
      <input type="text" id="<?php echo $this->get_field_id( 'user' ); ?>" name="<?php echo $this->get_field_name( 'user' ); ?>" value="<?php echo esc_attr( $user ); ?>" />

      <br/>

      <label for="<?php echo $this->get_field_id( 'count' ); ?>">Number of reviews (3-10):</label><br/>
      <input type="text" size="2" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $count ); ?>" />

      <br/>
<?php
/*
      <label for="<?php echo $this->get_field_id( 'members' ); ?>">Include All Team Member Reviews?</label><br/>
      <input type="checkbox" id="<?php echo $this->get_field_id( 'members' ); ?>" name="<?php echo $this->get_field_name( 'members' ); ?>" value="true" <?php if(!is_empty( $members )) { echo ( " checked=\"yes\" "); } ?> />
*/
?>
   </p><?php
  }


  // Apply settings to the widget instance.
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance[ 'title' ]   = strip_tags( $new_instance[ 'title'   ] );
    $instance[ 'zwsid' ]   = strip_tags( $new_instance[ 'zwsid'   ] );
    $instance[ 'count' ]   = strip_tags( $new_instance[ 'count'   ] );
    $instance[ 'user' ]    = strip_tags( $new_instance[ 'user'    ] );
    $instance[ 'members' ] = strip_tags( $new_instance[ 'members' ] );
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

