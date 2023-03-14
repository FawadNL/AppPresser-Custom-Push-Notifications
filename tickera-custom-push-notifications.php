<?php
/**
 * Adding a metabox inside Tickera Event edit page to send custom push notifications to all attendees of that event. This function requires AppPresser.
 *
 * @return void
 */
if ( ! function_exists( 'app_event_add_push_meta_box' ) ) {
	function app_event_add_push_meta_box() {
		add_meta_box(
			'event_push_notification',
			'Event Push Notification',
			'app_event_push_notification_box',
			'tc_events',
			'side',
			'high'
		);
	}
}
//fuction to add meta boxes.
add_action( 'add_meta_boxes', 'app_event_add_push_meta_box' );

if ( ! function_exists( 'push_to_attendees' ) ) {
	function push_to_attendees() {
		$eventId      = $_REQUEST['postid'];
		$push_message = $_REQUEST['push_message'];

		if ( ! $push_message ) {
			echo '<b>Please!</b> provide push message.';
			exit;
		}
		$args = array(
			'post_type'      => 'tc_tickets_instances',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'event_id', //(_event_name if used with WooCommerce)
					'compare' => '=',
					'value'   => $eventId,
				),
			),
		);

		$query     = new WP_Query( $args );
		$posts     = $query->posts;
		$attendees = array();
		foreach ( $posts as $post ) {
			$post_author = $post->post_author;
			array_push( $attendees, $post->post_author );
		}

		$attendies_count = count( $attendees );
		if ( $attendies_count <= 0 ) {
			echo '<b>Hello! </b>no attendees to notify.';
			exit;
		}

		if ( ! class_exists( 'AppPresser_Notifications' ) ) {
			echo '<b>Sorry! </b>AppPresser_Notifications class not available.';
			exit;
		}

		$recipients = $attendees;
		$message    = $push_message;
		$push       = new AppPresser_Notifications_Update();
		$devices    = $push->get_devices_by_user_id( $recipients );
		if ( $devices ) {
			$push->notification_send( 'now', $message, 1, $devices );
		}
		echo '<b>Hello! </b>Notification send successfully.';
		exit;
	}
}
add_action( 'wp_ajax_push_to_attendees', 'push_to_attendees' );
/**
 * Output the HTML for the call-to-action metabox.
 */
if ( ! function_exists( 'app_event_push_notification_box' ) ) {
	function app_event_push_notification_box() {
		global $post;
		?>
		<div class="notice notice-success" id="push_success_resp"></div>
		<div>
			<textarea name="ep_push_message" id="ep_push_message" class="widefat" cols="50" rows="5">Notification message here.</textarea>
		</div>

		<div>
			<a href="#" id="app_event_send_push" class="button">Push to Attendees</a>
		</div>


		<script>
			jQuery(function($){


				$( "#event_push_notification #app_event_send_push" ).on("click", function(e){
					$push_message=$('#ep_push_message').val();
					e.preventDefault();
					$.post(
						ajaxurl,
						{
							action: 'push_to_attendees',
							push_message:$push_message,
							postid: <?php echo $post->ID; ?>
						},
						function( response ){
							$('#push_success_resp').html(response);
						}
					);
				});
			});
		</script>
		<?php
	}
}
