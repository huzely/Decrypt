<?php
/**
 * Video player renderer and like/view frontend handlers.
 */
class AVSTube_VM_Video_Player {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'avstube_video_player', array( $this, 'render_player_shortcode' ) );
		add_shortcode( 'avstube_video_grid', array( $this, 'render_video_grid_shortcode' ) );
		add_shortcode( 'avstube_homepage', array( $this, 'render_homepage_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue player scripts.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'avstube-vm-player', AVSTUBE_VM_PLUGIN_URL . 'assets/js/avstube-player.js', array(), AVSTUBE_VM_VERSION, true );
		wp_localize_script(
			'avstube-vm-player',
			'avstubeVM',
			array(
				'restUrl' => esc_url_raw( rest_url( 'avstube/v1/' ) ),
			)
		);
	}

	/**
	 * Render single video player.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_player_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => get_the_ID(),
			),
			$atts,
			'avstube_video_player'
		);

		$video_id   = absint( $atts['id'] );
		$embed_url  = get_post_meta( $video_id, 'video_embed_url', true );
		$mp4_url    = get_post_meta( $video_id, 'video_mp4_url', true );
		$poster_url = get_the_post_thumbnail_url( $video_id, 'large' );
		$likes      = (int) get_post_meta( $video_id, 'video_likes', true );

		if ( empty( $video_id ) ) {
			return '';
		}

		ob_start();
		?>
		<div class="avstube-player-wrap" data-video-id="<?php echo esc_attr( (string) $video_id ); ?>">
			<?php if ( ! empty( $embed_url ) ) : ?>
				<div class="avstube-embed-container">
					<iframe src="<?php echo esc_url( $embed_url ); ?>" loading="lazy" allowfullscreen></iframe>
				</div>
			<?php elseif ( ! empty( $mp4_url ) ) : ?>
				<video class="avstube-player" controls preload="metadata" poster="<?php echo esc_url( $poster_url ); ?>">
					<source src="<?php echo esc_url( $mp4_url ); ?>" type="video/mp4">
					<?php esc_html_e( 'Your browser does not support HTML5 video.', 'avstube-video-manager' ); ?>
				</video>
			<?php else : ?>
				<p><?php esc_html_e( 'No playable source found for this video.', 'avstube-video-manager' ); ?></p>
			<?php endif; ?>

			<button class="avstube-like-button" type="button" data-video-id="<?php echo esc_attr( (string) $video_id ); ?>">
				❤ <span class="avstube-like-count"><?php echo esc_html( (string) $likes ); ?></span>
			</button>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render AVSTube video cards grid.
	 *
	 * @return string
	 */
	public function render_video_grid_shortcode() {
		$query = new WP_Query(
			array(
				'post_type'      => 'video',
				'post_status'    => 'publish',
				'posts_per_page' => 12,
			)
		);

		ob_start();
		?>
		<div class="video-grid">
			<?php if ( $query->have_posts() ) : ?>
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<a href="<?php the_permalink(); ?>" class="video-card">
						<div class="thumbnail">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy', 'class' => 'img-fluid' ) ); ?>
							<?php endif; ?>
							<span class="duration"><?php echo esc_html( (string) get_post_meta( get_the_ID(), 'video_duration', true ) ); ?></span>
						</div>
						<div class="card-info">
							<p class="title"><?php the_title(); ?></p>
							<p class="views">
								<?php echo esc_html( number_format_i18n( (int) get_post_meta( get_the_ID(), 'video_views', true ) ) ); ?>
								<?php esc_html_e( ' views • ', 'avstube-video-manager' ); ?>
								<?php echo esc_html( get_the_date() ); ?>
							</p>
						</div>
					</a>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No videos found.', 'avstube-video-manager' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Homepage shortcode adapted from provided HTML design for WordPress.
	 *
	 * @return string
	 */
	public function render_homepage_shortcode() {
		ob_start();
		?>
		<header class="header">
			<h1>AVSTube</h1>
			<p class="lead"><?php esc_html_e( 'Nền tảng chia sẻ video chất lượng cao', 'avstube-video-manager' ); ?></p>
			<p><?php esc_html_e( 'Phiên bản mới nhất: V1.0 (2026)', 'avstube-video-manager' ); ?></p>
			<form class="mt-3 mx-auto" style="max-width: 520px" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<div class="input-group">
					<input class="form-control bg-dark text-light border-secondary" name="s" placeholder="<?php esc_attr_e( 'Tìm kiếm video...', 'avstube-video-manager' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
					<button class="btn btn-danger" type="submit">🔎</button>
				</div>
			</form>
		</header>
		<section class="section"><div class="container"><h2 class="text-center mb-5"><?php esc_html_e( 'Video Nổi Bật', 'avstube-video-manager' ); ?></h2><?php echo do_shortcode( '[avstube_video_grid]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></section>
		<section class="section"><div class="container"><h2 class="text-center mb-5"><?php esc_html_e( 'Đánh giá từ người dùng', 'avstube-video-manager' ); ?></h2><div class="row justify-content-center"><div class="col-md-8"><div class="p-4 bg-dark rounded"><p><strong>Mike Felson / Webmaster</strong></p><p>"<?php esc_html_e( 'Nền tảng tốt nhất hiện nay. Đầy đủ tính năng, hỗ trợ HD, mobile mượt mà và đội ngũ hỗ trợ rất nhanh.', 'avstube-video-manager' ); ?>"</p></div></div></div></div></section>
		<footer><p><?php esc_html_e( 'Địa chỉ: Hà Nội, Việt Nam', 'avstube-video-manager' ); ?></p><p><?php esc_html_e( 'Email: support@avstube.vn', 'avstube-video-manager' ); ?></p><p><?php esc_html_e( 'Copyright © 2026 AVSTube. All Rights Reserved.', 'avstube-video-manager' ); ?></p></footer>
		<?php
		return (string) ob_get_clean();
	}
}
