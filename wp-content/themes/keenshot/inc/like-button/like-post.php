<?php

/*******************************
 * make sure ajax is working otherwise the like button won't work
*******************************/

function keenshot_add_ajax_url() {
    echo '<script type="text/javascript">var ajaxurl = "' . esc_url(admin_url('admin-ajax.php'),'keenshot') . '";</script>';
}
// Add hook for admin <head></head>
add_action('wp_head', 'keenshot_add_ajax_url');


/*******************************
 * likeCount:
 * Get current like count, this is used to show the amount of likes to the user
*******************************/

function keenshot_like_count($id){

   $keenshot_likes = get_post_meta( $id, '_likers', true );

   if(!empty($keenshot_likes)){
      return count(explode(', ', $keenshot_likes));
   }else{
      return '0';
   }

}



/*******************************
 * like_callback:
 * add or remove likes from the WordPress metabox field
*******************************/

add_action('wp_ajax_like_callback', 'keenshot_like_callback');
add_action('wp_ajax_nopriv_like_callback', 'keenshot_like_callback');

function keenshot_like_callback() {

   $ip = sanitize_text_field( wp_unslash( isset($_SERVER['REMOTE_ADDR']) )); 

   $id = json_decode(sanitize_text_field(wp_unslash(isset($_GET['data'])))); // Get the ajax call
   $feedback = array("likes" => "");

   // Get metabox values
   $currentvalue = get_post_meta( $id, '_likers', true );
   $keenshot_likes = intval(get_post_meta( $id, '_likes_count', true ));

   // Convert likers string to an array
   $likesarray = explode(', ', $currentvalue);


   // Check if the likers metabox already has a value to determine if the new entry has to be prefixed with a comma or not
   
   if(!empty($currentvalue)){
      $newvalue = $currentvalue .', '. $ip;
   }else{
      $newvalue = $ip;
   }


   // Check if the IP address is already present, if not, add it
   if(strpos($currentvalue, $ip) === false){
      $nlikes = $keenshot_likes + 1;
      if(update_post_meta($id, '_likers', $newvalue, $currentvalue) && update_post_meta($id, '_likes_count', $nlikes, $keenshot_likes)){
         $feedback = array("likes" => keenshot_like_count($id), "status" => true);
      }
   }else{

      $key = array_search($ip, $likesarray);
      unset($likesarray[$key]);
      $nlikes = $keenshot_likes - 1;

      if(update_post_meta($id, '_likers', implode(", ", $likesarray), $currentvalue) && update_post_meta($id, '_likes_count', $nlikes, $keenshot_likes)){
         $feedback = array("likes" => keenshot_like_count($id), "status" => false);
      }

   }

   echo json_encode($feedback);

   die(); // A kitten gif will be removed from the interwebs if you delete this line

}
