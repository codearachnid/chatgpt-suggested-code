<?php
/** prompt: 
 */

/**
 * Encapsulated REST API Client class
 */
class Encapsulated_REST_Client {
	/**
	 * The REST API endpoint URL
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * The REST API namespace
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The REST API version
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * The REST API authentication credentials
	 *
	 * @var array
	 */
	protected $credentials;

	/**
	 * Initialize the REST API client
	 *
	 * @param string $endpoint The REST API endpoint URL
	 * @param string $namespace The REST API namespace
	 * @param string $version The REST API version
	 * @param array $credentials The REST API authentication credentials
	 */
	public function __construct( $endpoint, $namespace, $version, $credentials ) {
		$this->endpoint = $endpoint;
		$this->namespace = $namespace;
		$this->version = $version;
		$this->credentials = $credentials;
	}

	/**
	 * Create a new post via the REST API
	 *
	 * @param array $data The post data
	 * @return array|WP_Error The response data or an error object
	 */
	public function create_post( $data ) {
		$url = sprintf( '%s/%s/%s/posts', $this->endpoint, $this->namespace, $this->version );
		$args = array(
			'method' => 'POST',
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->credentials['username'] . ':' . $this->credentials['password'] ),
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode( $data ),
		);

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$response_data = json_decode( $response_body, true );

		if ( $response_code !== 201 ) {
			return new WP_Error( $response_data['code'], $response_data['message'], $response_data['data'] );
		}

		return $response_data;
	}
}

// Instantiate the REST API client
$rest_client = new Encapsulated_REST_Client(
	'https://example.com/wp-json',
	'wp/v2',
	'2',
	array(
		'username' => 'your-username',
		'password' => 'your-password',
	)
);

// Create a new post
$result = $rest_client->create_post( array(
	'title' => 'New post',
	'content' => 'This is the content of the new post.',
	'status' => 'publish',
) );

// Check for errors and handle the response accordingly
if ( is_wp_error( $result ) ) {
	$error_message = $result->get_error_message();
	// handle the error
} else {
	// handle the response
}
