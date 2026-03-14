<?php
/**
 * Handle frontend video upload flow.
 */
class AVSTube_VM_Video_Upload {

	/**
	 * Max upload file size in bytes.
	 *
	 * @var int
	 */
	private $max_upload_size = 104857600; // 100MB.

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'avstube_video_upload_form', array( $this, 'render_upload_form' ) );
		add_action( 'init', array( $this, 'handle_form_submission' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue frontend CSS and JS.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'avstube-vm-style', AVSTUBE_VM_PLUGIN_URL . 'assets/css/avstube-video-manager.css', array(), AVSTUBE_VM_VERSION );
		wp_enqueue_script( 'avstube-vm-upload', AVSTUBE_VM_PLUGIN_URL . 'assets/js/avstube-upload.js', array(), AVSTUBE_VM_VERSION, true );
	}

	/**
	 * Render frontend upload form for authenticated users.
	 *
	 * @return string
	 */
	public function render_upload_form() {
		if ( ! is_user_logged_in() ) {
			return '<p>' . esc_html__( 'You must be logged in to upload a video.', 'avstube-video-manager' ) . '</p>';
		}

		$categories = get_categories( array( 'hide_empty' => false ) );
		ob_start();
		?>
		<form class="avstube-upload-form" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'avstube_vm_frontend_upload_action', 'avstube_vm_frontend_upload_nonce' ); ?>
			<input type="hidden" name="avstube_vm_frontend_upload" value="1">

			<label for="avstube_video_title"><?php esc_html_e( 'Video Title', 'avstube-video-manager' ); ?></label>
			<input type="text" id="avstube_video_title" name="avstube_video_title" required>

			<label for="avstube_video_description"><?php esc_html_e( 'Description', 'avstube-video-manager' ); ?></label>
			<textarea id="avstube_video_description" name="avstube_video_description" rows="5" required></textarea>

			<label for="avstube_video_category"><?php esc_html_e( 'Category', 'avstube-video-manager' ); ?></label>
			<select id="avstube_video_category" name="avstube_video_category">
				<option value=""><?php esc_html_e( 'Select category', 'avstube-video-manager' ); ?></option>
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option>
				<?php endforeach; ?>
			</select>

			<label for="avstube_video_tags"><?php esc_html_e( 'Tags (comma separated)', 'avstube-video-manager' ); ?></label>
			<input type="text" id="avstube_video_tags" name="avstube_video_tags" placeholder="travel, tutorial">

			<label for="avstube_video_thumbnail"><?php esc_html_e( 'Thumbnail', 'avstube-video-manager' ); ?></label>
			<input type="file" id="avstube_video_thumbnail" name="avstube_video_thumbnail" accept="image/*">

			<label for="avstube_video_file"><?php esc_html_e( 'MP4 Upload', 'avstube-video-manager' ); ?></label>
			<input type="file" id="avstube_video_file" name="avstube_video_file" accept="video/mp4">

			<label for="avstube_video_external_mp4"><?php esc_html_e( 'External MP4 URL', 'avstube-video-manager' ); ?></label>
			<input type="url" id="avstube_video_external_mp4" name="avstube_video_external_mp4" placeholder="https://example.com/video.mp4">

			<label for="avstube_video_embed_url"><?php esc_html_e( 'Embed URL (optional iframe source)', 'avstube-video-manager' ); ?></label>
			<input type="url" id="avstube_video_embed_url" name="avstube_video_embed_url" placeholder="https://www.youtube.com/embed/...">

			<button type="submit"><?php esc_html_e( 'Submit Video', 'avstube-video-manager' ); ?></button>
		</form>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Handle upload form submission.
	 *
	 * @return void
	 */
	public function handle_form_submission() {
		if ( ! isset( $_POST['avstube_vm_frontend_upload'] ) ) {
			return;
		}

		if ( ! is_user_logged_in() || ! current_user_can( 'upload_files' ) ) {
			return;
		}

		if ( ! isset( $_POST['avstube_vm_frontend_upload_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['avstube_vm_frontend_upload_nonce'] ) ), 'avstube_vm_frontend_upload_action' ) ) {
			return;
		}

		$title       = isset( $_POST['avstube_video_title'] ) ? sanitize_text_field( wp_unslash( $_POST['avstube_video_title'] ) ) : '';
		$description = isset( $_POST['avstube_video_description'] ) ? wp_kses_post( wp_unslash( $_POST['avstube_video_description'] ) ) : '';
		$category    = isset( $_POST['avstube_video_category'] ) ? absint( wp_unslash( $_POST['avstube_video_category'] ) ) : 0;
		$tags        = isset( $_POST['avstube_video_tags'] ) ? sanitize_text_field( wp_unslash( $_POST['avstube_video_tags'] ) ) : '';

		$video_external_mp4 = isset( $_POST['avstube_video_external_mp4'] ) ? esc_url_raw( wp_unslash( $_POST['avstube_video_external_mp4'] ) ) : '';
		$video_embed_url    = isset( $_POST['avstube_video_embed_url'] ) ? esc_url_raw( wp_unslash( $_POST['avstube_video_embed_url'] ) ) : '';

		$video_id = wp_insert_post(
			array(
				'post_type'    => 'video',
				'post_title'   => $title,
				'post_content' => $description,
				'post_status'  => 'pending',
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $video_id ) ) {
			return;
		}

		if ( $category > 0 ) {
			wp_set_post_terms( $video_id, array( $category ), 'category' );
		}

		if ( ! empty( $tags ) ) {
			$tag_items = array_filter( array_map( 'trim', explode( ',', $tags ) ) );
			if ( ! empty( $tag_items ) ) {
				wp_set_post_terms( $video_id, $tag_items, 'post_tag' );
			}
		}

		$uploaded_mp4_url = $this->handle_mp4_upload();
		if ( $uploaded_mp4_url ) {
			update_post_meta( $video_id, 'video_mp4_url', esc_url_raw( $uploaded_mp4_url ) );
		} elseif ( ! empty( $video_external_mp4 ) && preg_match( '/\.mp4($|\?)/i', $video_external_mp4 ) ) {
			update_post_meta( $video_id, 'video_mp4_url', $video_external_mp4 );
		}

		if ( ! empty( $video_embed_url ) ) {
			update_post_meta( $video_id, 'video_embed_url', $video_embed_url );
		}

		$this->handle_thumbnail_upload( $video_id );
		update_post_meta( $video_id, 'video_views', 0 );
		update_post_meta( $video_id, 'video_likes', 0 );
	}

	/**
	 * Upload MP4 file and return URL.
	 *
	 * @return string
	 */
	private function handle_mp4_upload() {
		if ( empty( $_FILES['avstube_video_file']['name'] ) || empty( $_FILES['avstube_video_file']['tmp_name'] ) ) {
			return '';
		}

		$file = $_FILES['avstube_video_file'];

		if ( (int) $file['size'] > $this->max_upload_size ) {
			return '';
		}

		$file_type = wp_check_filetype( $file['name'] );
		if ( empty( $file_type['ext'] ) || 'mp4' !== strtolower( $file_type['ext'] ) ) {
			return '';
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		$upload = wp_handle_upload( $file, array( 'test_form' => false, 'mimes' => array( 'mp4' => 'video/mp4' ) ) );

		if ( isset( $upload['url'] ) ) {
			return $upload['url'];
		}

		return '';
	}

	/**
	 * Upload thumbnail and attach to video post.
	 *
	 * @param int $post_id Video post ID.
	 * @return void
	 */
	private function handle_thumbnail_upload( $post_id ) {
		if ( empty( $_FILES['avstube_video_thumbnail']['name'] ) || empty( $_FILES['avstube_video_thumbnail']['tmp_name'] ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$attachment_id = media_handle_upload( 'avstube_video_thumbnail', $post_id );
		if ( ! is_wp_error( $attachment_id ) ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}
}
