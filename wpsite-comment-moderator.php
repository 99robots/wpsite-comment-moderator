<?php
/*
Plugin Name: WPsite Comment Moderator
plugin URI:
Description: Add a new user role, Comment Moderator, that allows a new user to only manage comments.
version: 1.0
Author: WPSITE.net
Author URI: http://wpsite.net
License: GPL2
*/

/**
 * Global Definitions
 */

/* Plugin Name */

if (!defined('WPSITE_COMMENT_MODERATOR_PLUGIN_NAME'))
    define('WPSITE_COMMENT_MODERATOR_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

/* Plugin directory */

if (!defined('WPSITE_COMMENT_MODERATOR_PLUGIN_DIR'))
    define('WPSITE_COMMENT_MODERATOR_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPSITE_COMMENT_MODERATOR_PLUGIN_NAME);

/* Plugin url */

if (!defined('WPSITE_COMMENT_MODERATOR_PLUGIN_URL'))
    define('WPSITE_COMMENT_MODERATOR_PLUGIN_URL', WP_PLUGIN_URL . '/' . WPSITE_COMMENT_MODERATOR_PLUGIN_NAME);

/* Plugin verison */

if (!defined('WPSITE_COMMENT_MODERATOR_VERSION_NUM'))
    define('WPSITE_COMMENT_MODERATOR_VERSION_NUM', '1.0.0');


/**
 * Activatation / Deactivation
 */

register_activation_hook( __FILE__, array('WPsiteCommentModerator', 'register_activation'));

/**
 * Hooks / Filter
 */

add_action('init', array('WPsiteCommentModerator', 'load_textdomain'));
add_action('admin_menu', array('WPsiteCommentModerator', 'wpsite_comment_moderator_remove_menu'));

/**
 *  WPsiteCommentModerator main class
 *
 * @since 1.0.0
 * @using Wordpress 3.8
 */

class WPsiteCommentModerator {

	/* Properties */

	private static $text_domain = 'wpsite-comment-moderator';

	private static $prefix = 'wpsite_comment_moderator_';

	/**
	 * Load the text domain
	 *
	 * @since 1.0.0
	 */
	static function load_textdomain() {
		load_plugin_textdomain(self::$text_domain, false, WPSITE_COMMENT_MODERATOR_PLUGIN_DIR . '/languages');
	}

	/**
	 * Hooks to 'register_activation_hook'
	 *
	 * @since 1.0.0
	 */
	static function register_activation() {

		/* Check if multisite, if so then save as site option */

		if (is_multisite()) {
			add_site_option('wpsite_comment_moderator_version', WPSITE_COMMENT_MODERATOR_VERSION_NUM);
		} else {
			add_option('wpsite_comment_moderator_version', WPSITE_COMMENT_MODERATOR_VERSION_NUM);
		}

		remove_role('comment_moderator');

		add_role(
		    'comment_moderator',
		    __('Comment Moderator', self::$text_domain),
		    array(
		    	'read'					=> true,
		        'moderate_comments' 	=> true,
		        'edit_comment'			=> true,
		        'edit_others_posts'		=> true,
		        'edit_published_posts'	=> true,
		        'edit_posts'			=> true
		    )
		);
	}

	static function wpsite_comment_moderator_remove_menu() {

		$user = wp_get_current_user();

	    if (!empty($user) && in_array('comment_moderator', (array) $user->roles)) {
			remove_menu_page( 'edit.php' );
			remove_menu_page( 'tools.php' );

			$post_types = get_post_types('', 'names');

			foreach ($post_types as $post_type) {
				remove_menu_page("edit.php?post_type=$post_type");
			}
	    }
	}
}
?>