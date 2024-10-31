<?php

namespace PostShowcase\Admin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The main admin class.
 *
 * @since 1.0.0
 * @package PostShowcase
 */
class Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Create admin settings page under WordPress settings menu.
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add settings page under WordPress settings menu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Post Showcase', 'post-showcase' ),
			__( 'Post Showcase', 'post-showcase' ),
			'manage_options',
			'post-showcase',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Render settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			post_showcase()->flash_notice( __( 'You do not have sufficient permissions to access this page.', 'post-showcase' ), 'error' );
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Post Showcase Settings', 'post-showcase' ); ?></h1>
			<p><?php esc_html_e( 'Configure the settings for the Post Showcase plugin.', 'post-showcase' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
				<?php
				settings_fields( 'post_showcase' );
				do_settings_sections( 'post-showcase' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'post_showcase', 'pshowcase_settings', array( $this, 'sanitize_settings' ) );

		// Add settings section.
		add_settings_section(
			'pshowcase_general_settings',
			__( 'General Settings', 'post-showcase' ),
			array( $this, 'general_settings' ),
			'post-showcase'
		);

		// Add settings field to display the showcase shortcode.
		add_settings_field(
			'pshowcase_shortcode',
			__( 'Post Showcase Shortcode', 'post-showcase' ),
			array( $this, 'showcase_shortcode' ),
			'post-showcase',
			'pshowcase_general_settings'
		);

		// Add settings field to enable showcase shortcode.
		add_settings_field(
			'pshowcase_shortcode_is_enabled',
			__( 'Enable Shortcode', 'post-showcase' ),
			array( $this, 'shortcode_is_enabled' ),
			'post-showcase',
			'pshowcase_general_settings'
		);

		// Post Showcase default post type field.
		add_settings_field(
			'pshowcase_post_type',
			__( 'Post Type', 'post-showcase' ),
			array( $this, 'post_type' ),
			'post-showcase',
			'pshowcase_general_settings'
		);

		// Post limit field.
		add_settings_field(
			'pshowcase_post_limit',
			__( 'Post Limit', 'post-showcase' ),
			array( $this, 'post_limit' ),
			'post-showcase',
			'pshowcase_general_settings'
		);

		// Display columns field.
		add_settings_field(
			'pshowcase_display_columns',
			__( 'Display Columns', 'post-showcase' ),
			array( $this, 'display_columns' ),
			'post-showcase',
			'pshowcase_general_settings'
		);

		// Post Showcase posts orderby field.
		add_settings_field(
			'pshowcase_posts_orderby',
			__( 'Posts Orderby', 'post-showcase' ),
			array( $this, 'posts_orderby' ),
			'post-showcase',
			'pshowcase_general_settings'
		);

		// Post Showcase posts order field.
		add_settings_field(
			'pshowcase_posts_order',
			__( 'Posts Order', 'post-showcase' ),
			array( $this, 'posts_order' ),
			'post-showcase',
			'pshowcase_general_settings'
		);

		// Excerpt length field.
		add_settings_field(
			'pshowcase_excerpt_length',
			__( 'Excerpt Length', 'post-showcase' ),
			array( $this, 'excerpt_length' ),
			'post-showcase',
			'pshowcase_general_settings'
		);
	}

	/**
	 * Display general settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function general_settings() {
		echo '<p>' . esc_html__( 'Configure the Post Showcase general settings.', 'post-showcase' ) . '</p>';
	}

	/**
	 * Display the showcase shortcode field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function showcase_shortcode() {
		?>
		<input type="text" name="pshowcase_settings[showcase_shortcode]" id="pshowcase_settings[showcase_shortcode]" value="<?php echo esc_attr( '[pshowcase_shortcode post_type="post" limit="6" columns="3" orderby="date" order="DESC"]' ); ?>" class="large-text" readonly />
		<p class="description"><?php esc_html_e( 'Copy and paste the shortcode to display the Post Showcase content in multiple posts or pages. You can customize the shortcode attributes as needed and the default values will be used if the attribute is not provided.', 'post-showcase' ); ?></p>
		<?php
	}

	/**
	 * Display the shortcode is enabled field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function shortcode_is_enabled() {
		$shortcode_is_enabled = pshowcase_get_settings( 'shortcode_is_enabled' );
		?>
		<label for="pshowcase_settings[shortcode_is_enabled]">
			<input type="checkbox" name="pshowcase_settings[shortcode_is_enabled]" id="pshowcase_settings[shortcode_is_enabled]" value="yes" <?php checked( $shortcode_is_enabled, 'yes' ); ?> />
			<?php esc_html_e( 'Enable to display the Post Showcase content via shortcode.', 'post-showcase' ); ?>
		</label>
		<?php
	}

	/**
	 * Display post type field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function post_type() {
		$default_post_type = pshowcase_get_settings( 'post_type' );
		?>
		<select name="pshowcase_settings[post_type]" id="pshowcase_settings[post_type]" class="regular-text">
			<?php
			// Get all public post types.
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			// Remove attachment post type.
			unset( $post_types['attachment'] );

			if ( ! empty( $post_types ) ) {
				foreach ( $post_types as $post_type ) {
					?>
					<option value="<?php echo esc_attr( $post_type->name ); ?>" <?php selected( $default_post_type, $post_type->name ); ?>><?php echo esc_html( $post_type->label ); ?></option>
					<?php
				}
			} else {
				?>
				<option value=""><?php esc_html_e( 'No post types found.', 'post-showcase' ); ?></option>
				<?php
			}
			?>
		</select>
		<p class="description"><?php esc_html_e( 'Chose the default post type to display. Default is posts.', 'post-showcase' ); ?></p>
		<?php
	}

	/**
	 * Display post limit field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function post_limit() {
		$post_limit = pshowcase_get_settings( 'post_limit' );
		?>
		<input type="number" name="pshowcase_settings[post_limit]" id="pshowcase_settings[post_limit]" value="<?php echo esc_attr( $post_limit ); ?>" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Enter the number of posts to display. Leave empty to display posts with the default limit. Default is 6.', 'post-showcase' ); ?></p>
		<?php
	}

	/**
	 * Display columns field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function display_columns() {
		$display_columns = pshowcase_get_settings( 'display_columns' );
		?>
		<input type="number" name="pshowcase_settings[display_columns]" id="pshowcase_settings[display_columns]" value="<?php echo esc_attr( $display_columns ); ?>" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Enter the number of columns to display. Leave empty to display columns with the default limit. Default is 3.', 'post-showcase' ); ?></p>
		<?php
	}

	/**
	 * Display posts orderby field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function posts_orderby() {
		$posts_orderby = pshowcase_get_settings( 'posts_orderby' );

		// Grab the all available orderby options.
		$orderby_options = array(
			'date'          => esc_html__( 'Date', 'post-showcase' ),
			'title'         => esc_html__( 'Title', 'post-showcase' ),
			'menu_order'    => esc_html__( 'Menu Order', 'post-showcase' ),
			'rand'          => esc_html__( 'Random', 'post-showcase' ),
			'comment_count' => esc_html__( 'Comment Count', 'post-showcase' ),
		);
		?>
		<select name="pshowcase_settings[posts_orderby]" id="pshowcase_settings[posts_orderby]" class="regular-text">
			<?php
			foreach ( $orderby_options as $orderby_key => $orderby_label ) {
				?>
				<option value="<?php echo esc_attr( $orderby_key ); ?>" <?php selected( $posts_orderby, $orderby_key ); ?>><?php echo esc_html( $orderby_label ); ?></option>
				<?php
			}
			?>
		</select>
		<p class="description"><?php esc_html_e( 'Chose the default orderby option to display posts. Default is date.', 'post-showcase' ); ?></p>
		<?php
	}

	/**
	 * Display posts order field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function posts_order() {
		$posts_order = pshowcase_get_settings( 'posts_order' );

		// Grab the all available order options.
		$order_options = array(
			'ASC'  => esc_html__( 'Ascending (ASC)', 'post-showcase' ),
			'DESC' => esc_html__( 'Descending (DESC)', 'post-showcase' ),
		);
		?>
		<select name="pshowcase_settings[posts_order]" id="pshowcase_settings[posts_order]" class="regular-text">
			<?php
			foreach ( $order_options as $order_key => $order_label ) {
				?>
				<option value="<?php echo esc_attr( $order_key ); ?>" <?php selected( $posts_order, $order_key ); ?>><?php echo esc_html( $order_label ); ?></option>
				<?php
			}
			?>
		</select>
		<p class="description"><?php esc_html_e( 'Chose the default order option to display posts. Default is descending (DESC).', 'post-showcase' ); ?></p>
		<?php
	}

	/**
	 * Display excerpt length field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function excerpt_length() {
		$excerpt_length = pshowcase_get_settings( 'excerpt_length' );
		?>
		<input type="number" name="pshowcase_settings[excerpt_length]" id="pshowcase_settings[excerpt_length]" value="<?php echo esc_attr( $excerpt_length ); ?>" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Enter the number of words to display in the excerpt. Leave empty to display the default excerpt length. Default is 20.', 'post-showcase' ); ?></p>
		<?php
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $settings Settings to sanitize.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function sanitize_settings( $settings ) {
		$sanitized_settings = array();

		// Sanitize the showcase shortcode. This field is read-only. No need to sanitize. Just set it to empty.
		$sanitized_settings['showcase_shortcode'] = '';

		// Sanitize the shortcode is enabled.
		$sanitized_settings['shortcode_is_enabled'] = isset( $settings['shortcode_is_enabled'] ) ? 'yes' : 'no';

		// Sanitize the post type.
		$sanitized_settings['post_type'] = isset( $settings['post_type'] ) ? sanitize_text_field( $settings['post_type'] ) : '';

		// Sanitize the post limit.
		$sanitized_settings['post_limit'] = isset( $settings['post_limit'] ) ? absint( $settings['post_limit'] ) : absint( 6 );

		// Sanitize the display columns.
		$sanitized_settings['display_columns'] = isset( $settings['display_columns'] ) ? absint( $settings['display_columns'] ) : absint( 3 );

		// Sanitize the posts orderby.
		$sanitized_settings['posts_orderby'] = isset( $settings['posts_orderby'] ) ? sanitize_text_field( $settings['posts_orderby'] ) : 'date';

		// Sanitize the posts order.
		$sanitized_settings['posts_order'] = isset( $settings['posts_order'] ) ? sanitize_text_field( $settings['posts_order'] ) : 'DESC';

		// Sanitize the excerpt length.
		$sanitized_settings['excerpt_length'] = isset( $settings['excerpt_length'] ) ? absint( $settings['excerpt_length'] ) : absint( 20 );

		return $sanitized_settings;
	}
}
