<?php
/**
 * Register video post type and meta fields.
 */
class AVSTube_VM_Video_Post_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_video_meta' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_video', array( $this, 'save_video_meta' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}

	/**
	 * Register the video custom post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Videos', 'avstube-video-manager' ),
			'singular_name'      => __( 'Video', 'avstube-video-manager' ),
			'add_new'            => __( 'Add New Video', 'avstube-video-manager' ),
			'add_new_item'       => __( 'Add New Video', 'avstube-video-manager' ),
			'edit_item'          => __( 'Edit Video', 'avstube-video-manager' ),
			'new_item'           => __( 'New Video', 'avstube-video-manager' ),
			'view_item'          => __( 'View Video', 'avstube-video-manager' ),
			'search_items'       => __( 'Search Videos', 'avstube-video-manager' ),
			'not_found'          => __( 'No videos found', 'avstube-video-manager' ),
			'not_found_in_trash' => __( 'No videos found in trash', 'avstube-video-manager' ),
			'menu_name'          => __( 'AVSTube Videos', 'avstube-video-manager' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'videos' ),
			'show_in_rest'       => true,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'comments', 'author', 'custom-fields' ),
			'taxonomies'         => array( 'category', 'post_tag' ),
			'menu_icon'          => 'dashicons-video-alt3',
			'capability_type'    => 'post',
		);

		register_post_type( 'video', $args );
	}

	/**
	 * Register video post meta fields.
	 *
	 * @return void
	 */
	public function register_video_meta() {
		$meta_fields = array(
			'video_mp4_url'   => 'esc_url_raw',
			'video_embed_url' => 'esc_url_raw',
			'video_duration'  => 'sanitize_text_field',
			'video_views'     => 'absint',
			'video_likes'     => 'absint',
		);

		foreach ( $meta_fields as $field_key => $sanitize_cb ) {
			register_post_meta(
				'video',
				$field_key,
				array(
					'show_in_rest'      => true,
					'single'            => true,
					'type'              => in_array( $field_key, array( 'video_views', 'video_likes' ), true ) ? 'integer' : 'string',
					'auth_callback'     => '__return_true',
					'sanitize_callback' => $sanitize_cb,
				)
			);
		}
	}

	/**
	 * Add custom meta box for video fields.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'avstube_vm_video_meta',
			__( 'Video Details', 'avstube-video-manager' ),
			array( $this, 'render_meta_box' ),
			'video',
			'normal',
			'default'
		);
	}

	/**
	 * Render video meta box.
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'avstube_vm_video_meta_nonce', 'avstube_vm_video_meta_nonce_field' );

		$mp4_url   = get_post_meta( $post->ID, 'video_mp4_url', true );
		$embed_url = get_post_meta( $post->ID, 'video_embed_url', true );
		$duration  = get_post_meta( $post->ID, 'video_duration', true );
		$views     = (int) get_post_meta( $post->ID, 'video_views', true );
		$likes     = (int) get_post_meta( $post->ID, 'video_likes', true );
		?>
		<p>
			<label for="video_mp4_url"><strong><?php esc_html_e( 'MP4 URL', 'avstube-video-manager' ); ?></strong></label>
			<input type="url" class="widefat" id="video_mp4_url" name="video_mp4_url" value="<?php echo esc_attr( $mp4_url ); ?>">
		</p>
		<p>
			<label for="video_embed_url"><strong><?php esc_html_e( 'Embed URL (iframe)', 'avstube-video-manager' ); ?></strong></label>
			<input type="url" class="widefat" id="video_embed_url" name="video_embed_url" value="<?php echo esc_attr( $embed_url ); ?>">
		</p>
		<p>
			<label for="video_duration"><strong><?php esc_html_e( 'Duration', 'avstube-video-manager' ); ?></strong></label>
			<input type="text" class="widefat" id="video_duration" name="video_duration" placeholder="00:00" value="<?php echo esc_attr( $duration ); ?>">
		</p>
		<p>
			<label for="video_views"><strong><?php esc_html_e( 'Views', 'avstube-video-manager' ); ?></strong></label>
			<input type="number" class="widefat" id="video_views" name="video_views" min="0" value="<?php echo esc_attr( $views ); ?>">
		</p>
		<p>
			<label for="video_likes"><strong><?php esc_html_e( 'Likes', 'avstube-video-manager' ); ?></strong></label>
			<input type="number" class="widefat" id="video_likes" name="video_likes" min="0" value="<?php echo esc_attr( $likes ); ?>">
		</p>
		<?php
	}

	/**
	 * Save video meta fields.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_video_meta( $post_id ) {
		if ( ! isset( $_POST['avstube_vm_video_meta_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['avstube_vm_video_meta_nonce_field'] ) ), 'avstube_vm_video_meta_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'video_mp4_url'   => 'esc_url_raw',
			'video_embed_url' => 'esc_url_raw',
			'video_duration'  => 'sanitize_text_field',
			'video_views'     => 'absint',
			'video_likes'     => 'absint',
		);

		foreach ( $fields as $field => $sanitize_cb ) {
			if ( isset( $_POST[ $field ] ) ) {
				$value = call_user_func( $sanitize_cb, wp_unslash( $_POST[ $field ] ) );
				update_post_meta( $post_id, $field, $value );
			}
		}
	}

	/**
	 * Add AVSTube Videos admin menu for statistics and moderation shortcuts.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_menu_page(
			__( 'AVSTube Videos', 'avstube-video-manager' ),
			__( 'AVSTube Videos', 'avstube-video-manager' ),
			'edit_posts',
			'avstube-videos-dashboard',
			array( $this, 'render_admin_dashboard' ),
			'dashicons-video-alt3',
			6
		);
	}

	/**
	 * Render admin dashboard page for upload statistics.
	 *
	 * @return void
	 */
	public function render_admin_dashboard() {
		$stats = wp_count_posts( 'video' );

		$pending_count = isset( $stats->pending ) ? (int) $stats->pending : 0;
		$publish_count = isset( $stats->publish ) ? (int) $stats->publish : 0;
		$trash_count   = isset( $stats->trash ) ? (int) $stats->trash : 0;
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AVSTube Video Dashboard', 'avstube-video-manager' ); ?></h1>
			<p><?php esc_html_e( 'Manage submissions, moderation, and video metrics.', 'avstube-video-manager' ); ?></p>
			<ul>
				<li><strong><?php esc_html_e( 'Published Videos:', 'avstube-video-manager' ); ?></strong> <?php echo esc_html( (string) $publish_count ); ?></li>
				<li><strong><?php esc_html_e( 'Pending Review:', 'avstube-video-manager' ); ?></strong> <?php echo esc_html( (string) $pending_count ); ?></li>
				<li><strong><?php esc_html_e( 'Trashed Videos:', 'avstube-video-manager' ); ?></strong> <?php echo esc_html( (string) $trash_count ); ?></li>
			</ul>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=video&post_status=pending' ) ); ?>"><?php esc_html_e( 'Review Pending Videos', 'avstube-video-manager' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=video' ) ); ?>"><?php esc_html_e( 'Manage Videos', 'avstube-video-manager' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit-comments.php?comment_type=comment' ) ); ?>"><?php esc_html_e( 'Moderate Comments', 'avstube-video-manager' ); ?></a>
			</p>
		</div>
		<?php
	}
}
