<?php

namespace PostShowcase\Controllers;

/**
 * The main showcase class.
 *
 * @since 1.0.0
 * @package PostShowcase
 */
class Showcase {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add shortcode.
		add_shortcode( 'pshowcase_shortcode', array( $this, 'render_showcase' ) );
	}

	/**
	 * Render showcase.
	 *
	 * @param array  $atts The shortcode attributes.
	 * @param string $content The shortcode content.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function render_showcase( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'post_type' => '',
				'limit'     => '',
				'columns'   => '',
				'orderby'   => '',
				'order'     => '',
			),
			$atts,
			'pshowcase_shortcode'
		);

		// Check if the post_type is empty. If so, get the default post type from the settings.
		if ( empty( $atts['post_type'] ) ) {
			$atts['post_type'] = pshowcase_get_settings( 'post_type', 'post' );
		}

		// Check if the limit is empty. If so, get the default limit from the settings.
		if ( empty( $atts['limit'] ) ) {
			$atts['limit'] = pshowcase_get_settings( 'post_limit', absint( 6 ) );
		}

		// Check if the columns is empty. If so, get the default columns from the settings.
		if ( empty( $atts['columns'] ) ) {
			$columns         = pshowcase_get_settings( 'display_columns', absint( 3 ) );
			$atts['columns'] = empty( $columns ) ? absint( 3 ) : absint( $columns );
		}

		// Check if the orderby is empty. If so, get the default orderby from the settings.
		if ( empty( $atts['orderby'] ) ) {
			$atts['orderby'] = pshowcase_get_settings( 'posts_orderby', 'date' );
		}

		// Check if the order is empty. If so, get the default order from the settings.
		if ( empty( $atts['order'] ) ) {
			$atts['order'] = pshowcase_get_settings( 'posts_order', 'DESC' );
		}

		// Check if the post type is valid or registered.
		if ( ! post_type_exists( $atts['post_type'] ) ) {
			return '';
		}

		// Get the posts.
		$posts = get_posts(
			array(
				'post_type'      => empty( $atts['post_type'] ) ? 'post' : sanitize_text_field( $atts['post_type'] ),
				'posts_per_page' => empty( $atts['limit'] ) ? absint( 6 ) : absint( $atts['limit'] ),
				'orderby'        => empty( $atts['orderby'] ) ? 'date' : sanitize_text_field( $atts['orderby'] ),
				'order'          => empty( $atts['order'] ) ? 'DESC' : sanitize_text_field( $atts['order'] ),
			)
		);

		// Check if there are posts.
		if ( empty( $posts ) ) {
			return '';
		}

		// Get the excerpt length.
		$excerpt_length = pshowcase_get_settings( 'excerpt_length', absint( 20 ) );

		// Check if the excerpt length is empty. If so, set the default excerpt length to 20.
		if ( empty( $excerpt_length ) ) {
			$excerpt_length = absint( 20 );
		}

		// Start the output buffer.
		ob_start();
		?>
		<div class="post-showcase">
			<?php if ( ! empty( $content ) ) : ?>
				<div class="showcase__header">
					<h2 class="showcase__header-title"><?php echo esc_html( $content ); ?></h2>
				</div>
			<?php endif; ?>
			<div class="showcase__items" style="grid-template-columns: repeat(<?php echo esc_attr( $atts['columns'] ); ?>, 1fr);">
			<?php
			foreach ( $posts as $post ) {
				// Excerpt the content of the post to 20 words.
				$post_excerpt = wp_trim_words( $post->post_content, $excerpt_length, '...' );
				?>
				<div class="showcase__item">
					<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
							<div class="showcase__item-thumbnail" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'large' ) ); ?>');"></div>
						</a>
					<?php endif; ?>
					<div class="showcase__item-content">
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="showcase__item-title-link">
							<h3 class="showcase__item-title"><?php echo esc_html( $post->post_title ); ?></h3>
						</a>
						<p class="showcase__item-excerpt">
							<?php echo wp_kses_post( $post_excerpt ); ?>
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="showcase__item-read-more"><?php esc_html_e( 'Read more', 'post-showcase' ); ?></a>
						</p>
					</div>
				</div>
				<?php
			}
			?>
			</div>
		</div>
		<?php

		// Return the output buffer.
		return ob_get_clean();
	}
}
