<?php
/*
Plugin Name: Catfish Manager
Plugin URI: http://www.thinkers.com/wordpress/2009/03/catfish-ad-plugin/
Description: The Catfish Manager allows you to setup and manage Catfish ads easily within Wordpress. Once activated <a href="admin.php?page=catfishmanager/catman.php">configure the options</a>.
Version: 1.0
Author: Thinkers
Author URI: http://www.thinkers.com/wordpress/2009/03/catfish-ad-plugin/
*/

function catman_init() {
	// Get the current options
	$text = get_option('catman_ad_text');
	$speed = get_option('catman_speed');
	$animation = get_option('catman_animation');
	$height = get_option('catman_height');
	$css = get_option('catman_css');
	$limitshow = get_option('catman_limitshow');
	$showlimit = get_option('catman_showlimit');

	// See if any options are empty (first run) and fill them with defaults
	if ( empty($text) ) {
		update_option('catman_ad_text', '<p>Congratulations! You have successfully installed the Catfish Manager plugin!</p>
<p>Go to the administration panel to change this text, the options and to set custom CSS to change how the advert looks.</p>');
	}
	if ( empty($speed) ) {
		update_option('catman_speed', 'normal');
	}
	if ( empty($animation) ) {
		update_option('catman_animation', 'slide');
	}
	if ( empty($height) ) {
		update_option('catman_height', 75);
	}
	if ( empty($limitshow) ) {
		update_option('catman_limitshow', 'true');
	}
	if ( empty($showlimit) ) {
		update_option('catman_showlimit', '1');
	}
	if ( empty($css) ) {
		update_option('catman_css', 'div#catman-catfish {
background-color: #ccc;
border-top: 1px solid #aaa;
text-align: left;
}
div#catman-catfish div#catman-inner {
padding: 10px;
}
div#catman-catfish div#catman-inner p {
color: #000;
}
div#catman-catfish a#catfish-close {
position: absolute;
top: 5px;
right: 5px;
color: #000;
}');
	}
}
add_action('init', 'catman_init');

function catman_advert() {
	// Add the advert into the bottom of the page
	echo '<div id="catman-catfish">
		<script type="text/javascript">
		<!--
			jQuery(window).load(function(){
				jQuery(\'#catman-catfish\').catfish({
					closeLink: \'#catfish-close\',
					height: '.get_option('catman_height').',
					animation: \''.get_option('catman_animation').'\',
					speed: \''.get_option('catman_speed').'\',
					limitShow: '.get_option('catman_limitshow').',
					showLimit: \''.get_option('catman_showlimit').'\'
				});
			});
		//-->
		</script>
		<div id="catman-inner">'.stripslashes(get_option('catman_ad_text')).'</div>
		<a href="#" id="catfish-close">Close</a>
	</div>';
}
add_action('wp_footer', 'catman_advert');

function catman_header() {
	// Add the scripts and custom CSS to the header
	$catman_url = WP_PLUGIN_URL.'/catman/';
	wp_print_scripts('jquery');
	echo '<script type="text/javascript" src="'.$catman_url.'scripts/jquery.cookie.js"></script>';
	echo '<script type="text/javascript" src="'.$catman_url.'scripts/jquery.catfish.js"></script>';
	echo '<style type="text/css">'.stripslashes(get_option('catman_css')).'</style>';
}
add_action('wp_head', 'catman_header');

// ADMIN STUFF

add_action('admin_menu', 'catman_plugin_menu');
function catman_plugin_menu() {
	// Setup the admin menus
	add_menu_page('Catfish Overview', 'Catfish', 8, __FILE__, 'catman_admin_index');
	add_submenu_page(__FILE__, 'Catfish Advert Text', 'Advert Text', 8, 'catman-text', 'catman_admin_text');
	add_submenu_page(__FILE__, 'Catfish Options', 'Options', 8, 'catman-options', 'catman_admin_options');
	add_submenu_page(__FILE__, 'Catfish Custom CSS', 'Custom CSS', 8, 'catman-css', 'catman_admin_css');
}

function catman_admin_index() {
	// Setup the index page with information about the plugin and links to the other pages
	?>
	<div class="wrap">
		<h2>Catfish Plugin Overview</h2>
		<p>The Catfish plugin allows you to display custom adverts which slide up from the bottom of the screen. There are different from most adverts as they catch the users attention when they slide in and always stay in the same play on the screen, even when the user scrolls.</p>
		<p>This plugin allows you to easily setup and manage your catfish advert. Start by setting the text in the advert. Then set the height of it using the dedicated tool. After you have done this the advert is ready to be displayed; however you can tweak the style and layout of your advert by using the custom css controls.</p>
		<ol style="font-size: 1.5em; list-style-type: decimal; margin-left: 55px;">
			<li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=catman-text">Advert Text</a></li>
			<li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=catman-options">Setup Height and Other Options</a></li>
			<li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=catman-css">Setup Custom CSS</a></li>
		</ol>
	</div>
	<?php
}

function catman_admin_text() {
	// Admin page for changing the text of the advert

	// variables for the field and option names
	$opt_name = 'catman_ad_text';
	$hidden_field_name = 'catman_submit_hidden';
	$data_field_name = 'content';

	// Read in existing option value from database
	$opt_val = stripslashes(get_option($opt_name));

	if( $_POST[$hidden_field_name] == 'Y' ) {
		// Read their posted value
		$opt_val = stripslashes($_POST[$data_field_name]);

		// Save the posted value in the database
		update_option($opt_name, $opt_val);

		// Put an options updated message on the screen

		?>
		<div class="updated"><p><strong><?php _e('Advert Text saved.', 'mt_trans_domain'); ?></strong></p></div>
		<?php

	}

	// Now display the options editing screen
	echo '<div class="wrap">';

	// header
	echo "<h2>" . __( 'Edit Advert Text', 'mt_trans_domain' ) . "</h2>";

	// options form
	wp_admin_css('thickbox');
	wp_print_scripts('jquery-ui-core');
	wp_print_scripts('jquery-ui-tabs');
	wp_print_scripts('editor');
	add_thickbox();
	?>
	<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

	<div id="quicktags">
	<script type='text/javascript'>
	/* <![CDATA[ */
		quicktagsL10n = {
			quickLinks: "(Quick Links)",
			wordLookup: "Enter a word to look up:",
			dictionaryLookup: "Dictionary lookup",
			lookup: "lookup",
			closeAllOpenTags: "Close all open tags",
			closeTags: "close tags",
			enterURL: "Enter the URL",
			enterImageURL: "Enter the URL of the image",
			enterImageDescription: "Enter a description of the image"
		}
		try{convertEntities(quicktagsL10n);}catch(e){};
	/* ]]> */
	</script>
	<script type='text/javascript' src='http://localhost/dev/catfish_plugin/wp-includes/js/quicktags.js?ver=20081210'></script>
	<script type="text/javascript">edToolbar()</script>
	</div>

	<div id='editorcontainer'><textarea rows='10' cols='40' name='content' tabindex='2' id='content'><?php echo $opt_val; ?></textarea></div>
	<script type="text/javascript">
		// <![CDATA[
		edCanvas = document.getElementById('content');
		var dotabkey = true;
		// ]]>
	</script>
		<hr />
		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Text', 'mt_trans_domain' ) ?>" />
			<span> or <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=catman/catman.php">back to overview</a></span>
		</p>
	</form>
	</div>
	<?php
}

function catman_admin_options() {
	// Admin page for changing the options of the plugin
	wp_print_scripts('jquery-ui-core');
	wp_print_scripts('jquery-ui-resizable');
	?>
	<div class="wrap">
		<h2>Catfish Plugin Options</h2>
		<?php
		if( $_POST['hidden_field'] == 'Y' ) {
			update_option('catman_height', $_POST['height']);
			update_option('catman_speed', $_POST['speed']);
			update_option('catman_animation', $_POST['animation']);
			update_option('catman_showlimit', $_POST['showlimit']);
			if ( !isset($_POST['limitshow']) ) {
				update_option('catman_limitshow', 'false');
			}
			else {
				update_option('catman_limitshow', 'true');
			}

			// Put an options updated message on the screen
			?>
			<div class="updated"><p><strong><?php _e('Options Updated.', 'mt_trans_domain'); ?></strong></p></div>
			<?php

		}
		?>
		<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="hidden_field" value="Y">

			<!--<div id="catman-catfish" style="position: relative; width: 70%; height: <?php get_option('catman_height'); ?>; margin: 0 auto;">
				<div id="catman-inner"><?php echo get_option('catman_ad_text'); ?></div>
				<a href="#" id="catfish-close">Close</a>
			</div>
			<script type="text/javascript">
				jQuery('#catman-catfish').resizable({
					handles: 's',
					stop: function(event, ui){
						jQuery('#catman-height').val(ui.size['height']);
					}
				});
			</script>-->

			<p><label for="height"><strong>Height: </strong></label><input type="text" id="catman-height" name="height" value="<?php echo get_option('catman_height'); ?>" /><span style="font-style: italic;">px</span></p>
			<p>
				<label for="animation"><strong>Animation: </strong></label>
				<select name="speed">
					<option value="slow"<?php if ( get_option('catman_speed') == 'slow' ) echo ' selected="selected"'; ?>>Slow</option>
					<option value="normal"<?php if ( get_option('catman_speed') == 'normal' ) echo ' selected="selected"'; ?>>Normal</option>
					<option value="fast"<?php if ( get_option('catman_speed') == 'fast' ) echo ' selected="selected"'; ?>>Fast</option>
				</select>
				<select name="animation">
					<option value="fade"<?php if ( get_option('catman_animation') == 'fade' ) echo ' selected="selected"'; ?>>Fade</option>
					<option value="slide"<?php if ( get_option('catman_animation') == 'slide' ) echo ' selected="selected"'; ?>>Slide</option>
				</select>
			</p>
			<p><label for="limitshow"><strong>Only show </strong><input type="text" name="showlimit" style="width: 35px; text-align: center;" value="<?php echo get_option('catman_showlimit'); ?>" /> <strong>time(s)? </strong></label><input type="checkbox" id="catman-limitshow" name="limitshow"<?php if ( get_option('catman_limitshow') == 'true' ) { echo ' checked="checked" '; } ?>/></p>

			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
				<span> or <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=catman/catman.php">back to overview</a></span>
			</p>
		</form>
		<p><a href="#" id="cookie-clear">Clear Cookie</a> <em>(For debugging)</em></p>
		<script type="text/javascript">
			jQuery('#cookie-clear').click(function(){
				jQuery.cookie('catman_enable', null, {path: '/'});
				alert('Your cookie has been deleted. You should now see the catfish advert again');
			});
		</script>
	</div>
	<?php
}

function catman_admin_options_head() {
	// Adds some cutstom CSS and scripts to the admin header
	echo '<script type="text/javascript" src="'.get_option('siteurl').'/wp-content/plugins/catman/scripts/jquery.js"></script>';
	echo '<script type="text/javascript" src="'.get_option('siteurl').'/wp-content/plugins/catman/scripts/jquery.cookie.js"></script>';
	echo '<style type="text/css">'.get_option('catman_css').'</style>';
}
add_action('admin_head', 'catman_admin_options_head');

function catman_admin_css() {
	// Admin page for changing the custom css
	?>
	<div class="wrap">
		<h2>Catfish Plugin Custom CSS</h2>
		<?php
		if( $_POST['hidden_field'] == 'Y' ) {
			update_option('catman_css', $_POST['css']);

			// Put an options updated message on the screen
			?>
			<div class="updated"><p><strong><?php _e('Custom CSS Updated.', 'mt_trans_domain'); ?></strong></p></div>
			<?php

		}
		?>
		<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="hidden_field" value="Y">

			<textarea name="css" col="40" rows="18" style="width: 100%;"><?php echo stripslashes(get_option('catman_css')); ?></textarea>

			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Update CSS', 'mt_trans_domain' ) ?>" />
				<span> or <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=catman/catman.php">back to overview</a></span>
			</p>
		</form>
	</div>
	<?php
}

?>