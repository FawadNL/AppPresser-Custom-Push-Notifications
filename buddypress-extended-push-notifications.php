<?php
// BuddyPress Extended Push Notifications: 
// Friendship request/accepted, group membership, tagging a person in comments.

//Remove this if you have already added it through WC Push Notifications script
add_action( 'wp_enqueue_scripts', 'appp_custom_scripts' );
function appp_custom_scripts() {
	wp_enqueue_script( 'appp-custom', get_stylesheet_directory_uri( 'js/apppresser-custom.js' , __FILE__ ), array('jquery', 'cordova-core'), 1.0 );
}

add_action('friends_friendship_requested', 'bp_send_friend_request', 10, 3);
function bp_send_friend_request( $friendship_id, $friendship_initiator_id, $friendship_friend_id ) {
    $device_ids = [];
    //$fp = fopen("testpush.txt", "w");
    
    //fwrite($fp, "\n New friendship requested...");
    //fwrite($fp, "\n friendship_id : ".$friendship_id);
    //fwrite($fp, "\n friendship_initiator_id : ".$friendship_initiator_id);
    //fwrite($fp, "\n friendship_friend_id : ".$friendship_friend_id);
    
    $user_info = get_userdata($friendship_initiator_id);
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;

    //fwrite($fp, "\n fname : ".$first_name);
    
    $recipients = array( $friendship_friend_id );
    $message = 'You have a friendship request from '.$first_name.' '.$last_name;

    //fwrite($fp, "\n message : ".$message);

	$push = new AppPresser_Notifications_Update;
	$devices = $push->get_devices_by_user_id( $recipients );

	if( $devices ) {
		$push->notification_send( 'now', $message, 1, $devices );
	}
    //fclose($fp);
}

add_action('friends_friendship_accepted', 'bp_accept_friend_request', 10, 3);
function bp_accept_friend_request( $friendship_id, $friendship_initiator_id, $friendship_friend_id ) {
    $device_ids = [];
    //$fp = fopen("testpush.txt", "w");
    
    //fwrite($fp, "\n friendship accepted...");
    //fwrite($fp, "\n friendship_id : ".$friendship_id);
    //fwrite($fp, "\n friendship_initiator_id : ".$friendship_initiator_id);
    //fwrite($fp, "\n friendship_friend_id : ".$friendship_friend_id);
    
    $user_info = get_userdata($friendship_friend_id);
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;

    //fwrite($fp, "\n fname : ".$first_name);
    
    $recipients = array( $friendship_initiator_id );
    $message =  $first_name.' '.$last_name.' accepted your friend request';

    //fwrite($fp, "\n message : ".$message);

	$push = new AppPresser_Notifications_Update;
	$devices = $push->get_devices_by_user_id( $recipients );

	if( $devices ) {
		$push->notification_send( 'now', $message, 1, $devices );
	}
    //fclose($fp);
}


add_action('groups_membership_requested', 'bp_send_group_request', 10, 3);
function bp_send_group_request($requesting_user_id,  $admins,  $group_id) {
    $device_ids = [];
    //$fp = fopen("testpush.txt", "w");
    
    //fwrite($fp, "\n Group join request...");
    //fwrite($fp, "\n requesting_user_id : ".$requesting_user_id);
    //fwrite($fp, "\n admins : ".$admins);
    //fwrite($fp, "\n group_id : ".$group_id);
    
    for($i = 0; $i < count($admins); $i++){
        //fwrite($fp, "\n i : ".$i);

        $adminid = $admins[0]->user_id;

        //fwrite($fp, "\n admin : ".$adminid);

        $user_info = get_userdata($requesting_user_id);
        $first_name = $user_info->first_name;
        $last_name = $user_info->last_name;

        //fwrite($fp, "\n fname : ".$first_name);
        
        $recipients = array( $adminid );
        $message =  $first_name.' '.$last_name.' requested membership to group';

        //fwrite($fp, "\n message : ".$message);

        $push = new AppPresser_Notifications_Update;
        $devices = $push->get_devices_by_user_id( $recipients );

        if( $devices ) {
            $push->notification_send( 'now', $message, 1, $devices );
        }

    }
    //fclose($fp);
}

add_action( 'groups_accept_invite', 'action_groups_accept_invite', 5, 3 );
function action_groups_accept_invite( $user_id, $group_id, $inviter_id ) {
    //$fp = fopen("testpushnew.txt", "w");
    
    //fwrite($fp, "\n Group invite accepted...");
    //fwrite($fp, "\n user_id : ".$user_id);
    //fwrite($fp, "\n group_id : ".$group_id);
    //fwrite($fp, "\n inviter_id : ".$inviter_id);

    
    $userid = $args['user_id'];
    // fwrite($fp, "\n useridss : ".$userid);

    $group = groups_get_group( array( 'group_id' => $group_id) );
    // fwrite($fp, "\n group : ".$group->name);

    $user_info = get_userdata($userid);
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;

    // fwrite($fp, "\n fname : ".$first_name);
        
    $recipients = array( $inviter_id );
    $message =  $first_name.' '.$last_name.' accepted your invitation to join group '.$group->name;

    // fwrite($fp, "\n message : ".$message);

    $push = new AppPresser_Notifications_Update;
    $devices = $push->get_devices_by_user_id( $recipients );

    if( $devices ) {
         $push->notification_send( 'now', $message, 1, $devices );
     }
    //fclose($fp);
}; 

add_action( 'groups_invite_user', 'action_groups_invite_user', 10, 1 ); 
function action_groups_invite_user($args) {
    //$fp = fopen("testpush.txt", "w");
    
    //fwrite($fp, "\n Group join invite...");
    //fwrite($fp, "\n args : ".$args);
    
    $userid = $args['user_id'];
    //fwrite($fp, "\n useridss : ".$userid);

    $group = groups_get_group( array( 'group_id' => $args['group_id']) );
    //fwrite($fp, "\n group : ".$group->name);

    $user_info = get_userdata($args['inviter_id']);
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;

    //fwrite($fp, "\n fname : ".$first_name);
        
    $recipients = array( $userid );
    $message =  $first_name.' '.$last_name.' invited you to join group '.$group->name;

    //fwrite($fp, "\n message : ".$message);

    $push = new AppPresser_Notifications_Update;
    $devices = $push->get_devices_by_user_id( $recipients );

    if( $devices ) {
        $push->notification_send( 'now', $message, 1, $devices );
    }
    //fclose($fp);
}

add_action( 'comment_post', 'show_message_function', 10, 2 );
function show_message_function( $comment_ID, $comment_approved ) {
    //$fp = fopen("testpush.txt", "w");
    
    //fwrite($fp, "\n new comment posted...");
    //fwrite($fp, "\n comment_ID : ".$comment_ID);
    //fwrite($fp, "\n comment_approved : ".$comment_approved);

    $comment = get_comment( $comment_ID);

    $content = $comment->comment_content;
    $auther = $comment->comment_author;
    $postid = $comment->comment_post_ID;
    $post = get_post($postid);
    $postname = $post->post_title;

    //fwrite($fp, "\n content : ".$content);
    //fwrite($fp, "\n content : ".$auther);

    $matches = array();
    preg_match_all("/\@[a-z0-9_]+/i", $content, $matches, PREG_SET_ORDER);
    print_r($matches);

    // exit(0);

    for($i = 0; $i < count($matches); $i++){
        $firstuser = $matches[$i][0];
        //fwrite($fp, "\n firstuser : ".$firstuser);
        if($firstuser){
        $firstuser = str_replace("@","",$firstuser);

        }
        //fwrite($fp, "\n firstuser after : ".$firstuser);


        $user = get_user_by( 'login', $firstuser);
        //fwrite($fp, "\n userid : ".$user->ID);


        $userid = $user->ID;

        $recipients = array( $userid );
        $message =  $auther.' tagged you in the post '.$postname;

        //fwrite($fp, "\n message : ".$message);

        $push = new AppPresser_Notifications_Update;
        $devices = $push->get_devices_by_user_id( $recipients );

        if( $devices ) {
            $push->notification_send( 'now', $message, 1, $devices );
        }
    }

    //fclose($fp);
}
