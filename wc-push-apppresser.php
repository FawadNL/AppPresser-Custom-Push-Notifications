//*Add this code in your main WP theme Functions.php
//*You can also change/add push messages of the order status = $msg

// Woocommerce Push Notifications

add_action( 'wp_enqueue_scripts', 'appp_custom_scripts' );
function appp_custom_scripts() {
	wp_enqueue_script( 'appp-custom', get_stylesheet_directory_uri( 'js/apppresser-custom.js' , __FILE__ ), array('jquery', 'cordova-core'), 1.0 );
}

add_action( 'woocommerce_order_status_changed', 'order_status_changed_hook', 10, 3 );
function order_status_changed_hook($order_id, $old_status, $new_status)
{
    $fp = fopen("testpush.txt", "w");
    fwrite($fp, '\n order old status : '.$old_status);
    fwrite($fp, '\n order new status : '.$new_status);
    $order = wc_get_order( $order_id );
    $items = $order->get_data();
    $user_id = $order->get_user_id();
    $device_ids = get_user_meta( $user_id, 'ap3_endpoint_arns', 1 );
    
    $msg = '';
    if ($new_status == 'pending') {
        $msg = 'Your order is pending';
    } elseif ($new_status == 'processing') {
        $msg = 'Your order is processing';
    } elseif ($new_status == 'completed') {
        $msg = 'Your order is completed';
    }


    $recipients = array( $user_id );
    $message =  $msg;
    
    $push = new AppPresser_Notifications_Update;
    $devices = $push->get_devices_by_user_id( $recipients );

    if( $devices ) {
        $push->notification_send( 'now', $message, 1, $devices );
    }
}
