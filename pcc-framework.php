<?php
/**
 * Plugin Name:     PCC Framework
 * Plugin URI:      https://platform.coop
 * Description:     Utilities, custom post types and blocks for the Platform Cooperativism Consortium website.
 * Author:          Platform Cooperativism Consortium
 * Author URI:      https://platform.coop
 * Text Domain:     pcc-framework
 * Domain Path:     /languages
 * License:         BSD 3-Clause "New" License
 * License URI:     https://opensource.org/licenses/BSD-3-Clause
 * Version:         2.4.0
 *
 * @package         PCCFramework
 */

if (!defined('ABSPATH')) {
    die('Direct access to this file is not permitted.');
}

/**
 * Load utilities.
 */
require_once dirname(__FILE__) . '/lib/utils.php';

/**
 * Ensure Composer dependencies are present.
 */
if (!function_exists('register_extended_post_type')) {
    if (!file_exists($composer = __DIR__.'/vendor/autoload.php')) {
        \PCCFramework\Utils\error_handler(
            __(
                'You must run <code>composer install</code> from the Platform Co-op Support directory.',
                'pcc-framework'
            ),
            __('Autoloader not found.', 'pcc-framework')
        );
    }
    require_once $composer;
}

/**
 * Load CMB2.
 */
require_once __DIR__ . '/vendor/cmb2/cmb2/init.php';

/**
 * Load and register post types.
 */
foreach ([
    'attachment',
    'event',
    'person',
    'post',
    'project',
    'story'
] as $posttype) {
    require_once dirname(__FILE__) . "/lib/posttypes/pcc-$posttype.php";
    if ($posttype !== 'attachment') {
        if ($posttype !== 'post') {
            add_action('init', '\\PCCFramework\\PostTypes\\' . ucfirst($posttype) . '\\init');
        }
        add_action('init', '\\PCCFramework\\PostTypes\\' . ucfirst($posttype) . '\\register_meta');
    }
}

/**
 * Load and register taxonomies.
 */
foreach ([
    'region',
    'role',
    'sector',
    'organization',
] as $taxonomy) {
    require_once dirname(__FILE__) . "/lib/taxonomies/pcc-$taxonomy.php";
    add_action('init', '\\PCCFramework\\Taxonomies\\' . ucfirst($taxonomy) . '\\init');
}

/**
 * Load blocks.
 */
require_once dirname(__FILE__) . '/lib/blocks.php';

add_action('init', '\\PCCFramework\\Blocks\\register_block_assets');

foreach ([
    'child-pages',
    'participants-button',
    'program-button',
    'recent-content',
    'projects',
    'social-links',
] as $block) {
    require_once dirname(__FILE__) . "/lib/blocks/$block.php";
    $pieces = explode('-', $block);
    $pieces = array_map('ucfirst', $pieces);
    $block = implode('', $pieces);
    add_action('init', "\\PCCFramework\\Blocks\\$block\\register_block");
}

/**
 * Load admin assets and metadata fields.
 */
if (is_admin()) {
    require_once dirname(__FILE__) . '/lib/admin.php';
    require_once dirname(__FILE__) . '/lib/settings.php';

    add_action('admin_enqueue_scripts', '\\PCCFramework\\Admin\\enqueue_assets');
    add_action('enqueue_block_editor_assets', '\\PCCFramework\\Blocks\\enqueue_block_assets');
    add_action('cmb2_admin_init', '\\PCCFramework\\PostTypes\\Event\\data');
    add_action('cmb2_admin_init', '\\PCCFramework\\PostTypes\\Event\\sponsors');
    add_action('cmb2_admin_init', '\\PCCFramework\\PostTypes\\Person\\data');
    add_action('cmb2_admin_init', '\\PCCFramework\\PostTypes\\Post\\data');
    add_action('cmb2_admin_init', '\\PCCFramework\\PostTypes\\Project\\data');
    add_action('cmb2_admin_init', '\\PCCFramework\\PostTypes\\Story\\data');
    add_action('cmb2_admin_init', '\\PCCFramework\\Settings\\configuration');
    add_action('cmb2_admin_init', '\\PCCFramework\\Settings\\localization');
    add_filter('attachment_fields_to_edit', '\\PCCFramework\\PostTypes\\Attachment\\data', 10, 2);
    add_action('edit_attachment', '\\PCCFramework\\PostTypes\\Attachment\\save');
}

require_once dirname(__FILE__) . '/lib/intervention.php';
add_action('init', '\\PCCFramework\\Intervention\\apply_interventions');


/**
 * Register new embed providers.
 */
require_once dirname(__FILE__) . "/lib/embeds.php";

PCCFramework\Embeds\init_livestream();
