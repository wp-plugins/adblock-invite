<?php
/*
Plugin Name: Adblock invite (Yes Adblock)
Plugin URI: http://adblock-invite.zici.fr
Description: Detects if the visitor have AdBlock (or another) plugin/extension installed and if not, gives URL to download it.
Author: David Mercereau
Author URI: http://david.mercereau.info
Version: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// HowTo Based : 
// http://social.hecube.net/2011/07/wordpress-widget-plugin-creer-un-widget-a-laide-dun-plugin-pour-votre-theme-dans-wordpress/
// http://www.johnathanandersendesign.com/online/?page_id=66
// http://www.makeuseof.com/tag/how-to-create-wordpress-widgets/

add_action( 'widgets_init', create_function('', 'return register_widget("adblockInviteWidget");') );

function adblockInviteScriptFooter(){ 
	?>
	<script language="JavaScript" type="text/javascript" src="<?php echo plugins_url('testads/banners.js', __FILE__); ?>"></script>
	<script language="JavaScript" type="text/javascript">
		function widgetAdlblockInviteID(tag) {
                        var regexpParam=/adblock_invite_widget-*/;
                        var tagParam=tag;
                        tagParam = (tagParam === undefined) ? '*' : tagParam;
                        var elementsTable = new Array();
                        for(var i=0 ; i<document.getElementsByTagName(tagParam).length ; i++) {
                                if(document.getElementsByTagName(tagParam)[i].id && document.getElementsByTagName(tagParam)[i].id.match(regexpParam)) {
                                        document.getElementsByTagName(tagParam)[i].style.display='block';
                                }
                        }
		}
		var divAds = document.getElementById("adstest");
		if(divAds) {
			widgetAdlblockInviteID('div');
			widgetAdlblockInviteID('aside');
		}
	</script>
	<?php
} 

add_action('wp_footer', 'adblockInviteScriptFooter'); 
add_action('wp_print_styles', 'addstyle');

function addstyle() {
	//~ if (!is_admin()){
        wp_enqueue_style('adblock-invite-styles', plugins_url('style.css', __FILE__));
	//~ }
}

class adblockInviteWidget extends WP_Widget {

	function adblockInviteWidget() {
		$widget_ops = array( 
							'classname' => 'adblock_invite_widget_class', 
							'description' => 'Detects if the visitor have AdBlock (or another) plugin/extension installed and if not, gives URL to download it.' 
							); 
		$this->WP_Widget('adblock_invite_widget', 'Adblock invite', $widget_ops);
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'You do not block ads?', 'message' => '<p>It seems that you don\'t have AdBlock (or another) installed in your browser. I encourage you to install it for free</p> <p><a href="http://adblockplus.org" target="_blank">Download AdBlock</a></p>' ) );
		$title = $instance['title'];
		$message = $instance['message'];
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Title: </label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('message'); ?>">Message: </label>
				<textarea class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" cols="30" rows="4"><?php echo attribute_escape($message); ?></textarea>
			</p>
		<?php
	}


	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['message'] = $new_instance['message'];
		return $instance;
	}
	
	function widget($args, $instance) {
		extract($args);

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'] );
		$message = empty( $instance['message'] ) ? '&nbsp;' : $instance['message'];

		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
        
		echo $message;

		echo $after_widget;
	}
}

