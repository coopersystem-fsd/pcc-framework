<?php

namespace PCCFramework\Admin;

function enqueue_assets()
{
    wp_enqueue_style(
        'pcc-framework',
        plugin_dir_url(dirname(__FILE__)) . '/build/admin.css',
        false,
        PCC_FRAMEWORK_VERSION
    );

    wp_enqueue_script(
        'cmb2-conditional-logic',
        plugin_dir_url(dirname(__FILE__)) . '/src/WP-CMB2-conditional-logic-master/js/cmb2-conditional-logic.min.js',
        ['jquery'],
        '0.0.1',
        true
    );
}
