<?php
/**
 * Local Server for WordPress VIP Projects.
 *
 * @package humanmade/local-vip
 */

namespace HM\Local_VIP;

/**
 * Configure environment for local server.
 */
function bootstrap() {
	// Try reading HTTP Host from environment
	if ( empty( $_SERVER['HTTP_HOST'] ) ) {
		$_SERVER['HTTP_HOST'] = getenv( 'HTTP_HOST' );
	}
	// fall back to {project name}.local
	if ( empty( $_SERVER['HTTP_HOST'] ) ) {
		$_SERVER['HTTP_HOST'] = getenv( 'COMPOSE_PROJECT_NAME' ) . '.local';
	}

	if ( in_array( getenv( 'SUBDOMAIN_INSTALL' ), [ true, 1 ] ) ) {
		define( 'SUBDOMAIN_INSTALL', 1 );
	}

	defined( 'DB_HOST' ) or define( 'DB_HOST', getenv( 'DB_HOST' ) );
	defined( 'DB_USER' ) or define( 'DB_USER', getenv( 'DB_USER' ) );
	defined( 'DB_PASSWORD' ) or define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) );
	defined( 'DB_NAME' ) or define( 'DB_NAME', getenv( 'DB_NAME' ) );

	define( 'ELASTICSEARCH_HOST', getenv( 'ELASTICSEARCH_HOST' ) );
	define( 'ELASTICSEARCH_PORT', getenv( 'ELASTICSEARCH_PORT' ) );

	if ( ! defined( 'AWS_XRAY_DAEMON_IP_ADDRESS' ) ) {
		define( 'AWS_XRAY_DAEMON_IP_ADDRESS', gethostbyname( getenv( 'AWS_XRAY_DAEMON_HOST' ) ) );
	}

	add_filter( 'qm/output/file_path_map', __NAMESPACE__ . '\\set_file_path_map', 1 );
}

/**
 * Enables Query Monitor to map paths to their original values on the host.
 *
 * @param array $map Map of guest path => host path.
 * @return array Adjusted mapping of folders.
 */
function set_file_path_map( array $map ) : array {
	if ( ! getenv( 'HOST_PATH' ) ) {
		return $map;
	}
	$map['/usr/src/app'] = rtrim( getenv( 'HOST_PATH' ), DIRECTORY_SEPARATOR );
	return $map;
}

/**
 * Add new submenus to Tools admin menu.
 */
function tools_submenus() {
	$links = [
		[
			'label' => 'Kibana',
			'url' => network_site_url( '/kibana' ),
		],
		[
			'label' => 'MailHog',
			'url' => network_site_url( '/mailhog' ),
		],
	];

	foreach ( $links as $link ) {
		add_management_page( $link['label'], $link['label'], 'manage_options', $link['url'] );
	}
}
