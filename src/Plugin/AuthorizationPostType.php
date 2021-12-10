<?php
/**
 * Authorization Post Type
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;

/**
 * Authorization Post Type
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class AuthorizationPostType {
    public function __construct( $plugin ) {
        $this->plugin = $plugin;
    }

    public function setup() {
        \add_action( 'init', array( $this, 'init' ) );

        \add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
    }

    public function init() {
        \register_post_type(
            'twinfield_auth',
            array(
                'label'         => __( 'Twinfield Authorizations', 'pronamic-twinfield' ),
                'labels'        => array(
                    'menu_name' => __( 'Authorizations', 'pronamic-twinfield' ),
                ),
                'public'        => true,
                'show_in_menu'  => false,
                'supports'      => array(
                    '',
                ),
            )
        );
    }

    /**
     * Add meta boxes.
     * 
     * @param string  $post_type Post type.
     * @param WP_Post $post      Post object.
     */
    public function add_meta_boxes( $post_type, $post ) {
        if ( 'twinfield_auth' !== $post_type ) {
            return;
        }

        /**
         * Authentication/
         */
        $object = \json_decode( $post->post_content );

        if ( ! empty( $object ) ) {
            $authentication = AuthenticationInfo::from_object( $object );

            if ( $authentication ) {
                \add_meta_box(
                    'pronamic_twinfield_authentication',
                    \__( 'Authentication', 'twinfield' ),
                    array( $this, 'meta_box_authentication' ),
                    $post_type,
                    'normal',
                    'high'
                );

                \add_meta_box(
                    'pronamic_twinfield_offices',
                    \__( 'Offices', 'twinfield' ),
                    array( $this, 'meta_box_offices' ),
                    $post_type,
                    'normal',
                    'high'
                );
            }
        }

        \add_meta_box(
            'pronamic_twinfield_authorize',
            \__( 'Authorize', 'twinfield' ),
            array( $this, 'meta_box_authorize' ),
            $post_type,
            'normal',
            'high'
        );
    }

    /**
     * Meta box authorize.
     * 
     * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
     * @param WP_Post $post Post.
     * @param array   $box  Box.
     */
    public function meta_box_authorize( $post, $box ) {
        $plugin = $this->plugin;

        include __DIR__ . '/../../admin/meta-box-authorize.php';
    }

    /**
     * Meta box authentication.
     * 
     * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
     * @param WP_Post $post Post.
     * @param array   $box  Box.
     */
    public function meta_box_authentication( $post, $box ) {
        $plugin = $this->plugin;

        include __DIR__ . '/../../admin/meta-box-authentication.php';
    }

    /**
     * Meta box offices.
     * 
     * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
     * @param WP_Post $post Post.
     * @param array   $box  Box.
     */
    public function meta_box_offices( $post, $box ) {
        $plugin = $this->plugin;

        include __DIR__ . '/../../admin/meta-box-offices.php';
    }
}
