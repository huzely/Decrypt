<?php
/**
 * REST API handlers for video data and likes/views.
 */
class AVSTube_VM_Video_API {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'avstube/v1',
			'/video/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_video_data' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'avstube/v1',
			'/video/like/(?P<id>\d+)',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'like_video' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'avstube/v1',
			'/video/view/(?P<id>\d+)',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'increment_view' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Return video payload for player clients.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_video_data( WP_REST_Request $request ) {
		$video_id = absint( $request['id'] );
		$cache_key = 'avstube_video_api_' . $video_id;
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return rest_ensure_response( $cached );
		}

		$video = get_post( $video_id );
		if ( ! $video || 'video' !== $video->post_type ) {
			return new WP_REST_Response( array( 'message' => 'Video not found' ), 404 );
		}

		$data = array(
			'id'          => $video_id,
			'title'       => get_the_title( $video_id ),
			'description' => apply_filters( 'the_content', $video->post_content ),
			'thumbnail'   => get_the_post_thumbnail_url( $video_id, 'large' ),
			'mp4_url'     => get_post_meta( $video_id, 'video_mp4_url', true ),
			'embed_url'   => get_post_meta( $video_id, 'video_embed_url', true ),
			'views'       => (int) get_post_meta( $video_id, 'video_views', true ),
			'likes'       => (int) get_post_meta( $video_id, 'video_likes', true ),
			'upload_date' => get_the_date( 'c', $video_id ),
		);

		set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );

		return rest_ensure_response( $data );
	}

	/**
	 * Increment likes via AJAX/REST.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function like_video( WP_REST_Request $request ) {
		$video_id = absint( $request['id'] );
		$likes = (int) get_post_meta( $video_id, 'video_likes', true );
		$likes++;
		update_post_meta( $video_id, 'video_likes', $likes );
		delete_transient( 'avstube_video_api_' . $video_id );

		return new WP_REST_Response(
			array(
				'video_id' => $video_id,
				'likes'    => $likes,
			),
			200
		);
	}

	/**
	 * Increment view counter when playback starts.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function increment_view( WP_REST_Request $request ) {
		$video_id = absint( $request['id'] );
		$views = (int) get_post_meta( $video_id, 'video_views', true );
		$views++;
		update_post_meta( $video_id, 'video_views', $views );
		delete_transient( 'avstube_video_api_' . $video_id );

		return new WP_REST_Response(
			array(
				'video_id' => $video_id,
				'views'    => $views,
			),
			200
		);
	}
}
