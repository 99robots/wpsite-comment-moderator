<?php
/**
 * Plugin Name:		Comment Moderator
 * Plugin URI:		https://99robots.com/products/
 * Description:		Add a new user role, Comment Moderator, that allows a new user to only manage comments.
 * Version:			1.3.3
 * Author:			99 Robots
 * Author URI:		https://99robots.com
 * License:			GPL2
 * Text Domain:		wpsite-comment-moderator
 */

/**
 * Global Definitions
 */

// Plugin Name
if ( ! defined( 'WPSITE_COMMENT_MODERATOR_PLUGIN_NAME' ) ) {
	define( 'WPSITE_COMMENT_MODERATOR_PLUGIN_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

// Plugin directory
if ( ! defined( 'WPSITE_COMMENT_MODERATOR_PLUGIN_DIR' ) ) {
	define( 'WPSITE_COMMENT_MODERATOR_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPSITE_COMMENT_MODERATOR_PLUGIN_NAME );
}

// Plugin url
if ( ! defined( 'WPSITE_COMMENT_MODERATOR_PLUGIN_URL' ) ) {
	define( 'WPSITE_COMMENT_MODERATOR_PLUGIN_URL', WP_PLUGIN_URL . '/' . WPSITE_COMMENT_MODERATOR_PLUGIN_NAME );
}

// Plugin verison
if ( ! defined( 'WPSITE_COMMENT_MODERATOR_VERSION_NUM' ) ) {
	define( 'WPSITE_COMMENT_MODERATOR_VERSION_NUM', '1.3.2' );
}

/**
 * Activatation / Deactivation
 */
register_activation_hook( __FILE__, array( 'WPsiteCommentModerator', 'register_activation' ) );

/**
 * WPsiteCommentModerator
 *
 * @since 1.0.0
 */
class WPsiteCommentModerator {

	/**
	 * The Constructor
	 *
	 * @since 1.3.2
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_menu', array( $this, 'remove_menu' ) );
	}

	/**
	 * Load the text domain
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wpsite-comment-moderator', false, WPSITE_COMMENT_MODERATOR_PLUGIN_DIR . '/languages' );
	}

	/**
	 * Remove menu according to role
	 */
	public function remove_menu() {

		$user = wp_get_current_user();

		if ( ! empty( $user ) && in_array( 'comment_moderator', (array) $user->roles ) ) {

			remove_menu_page( 'edit.php' );
			remove_menu_page( 'tools.php' );

			$post_types = get_post_types( '', 'names' );

			foreach ( $post_types as $post_type ) {
				remove_menu_page( 'edit.php?post_type=' . $post_type );
			}
		}
	}

	/**
	 * Hooks to 'register_activation_hook'
	 *
	 * @since 1.0.0
	 */
	static function register_activation() {

		// Check if multisite, if so then save as site option
		if ( is_multisite() ) {
			add_site_option( 'wpsite_comment_moderator_version', WPSITE_COMMENT_MODERATOR_VERSION_NUM );
		} else {
			add_option( 'wpsite_comment_moderator_version', WPSITE_COMMENT_MODERATOR_VERSION_NUM );
		}

		remove_role( 'comment_moderator' );

		add_role(
			'comment_moderator',
			esc_html__( 'Comment Moderator', 'wpsite-comment-moderator' ),
			array(
				'read'					=> true,
				'moderate_comments' 	=> true,
				'edit_comment'			=> true,
				'edit_others_posts'		=> true,
				'edit_published_posts'	=> true,
				'edit_posts'			=> true,
				'edit_others_pages'		=> true,
				'edit_published_pages'	=> true,
				'edit_pages'			=> true,
			)
		);
	}
}

/**
 * Start the plugin
 */
new WPsiteCommentModerator;
