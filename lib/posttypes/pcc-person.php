<?php

namespace PlatformCoop\PostTypes\Person;

/**
 * Registers the `pcc-person` post type.
 */
function init()
{
    register_extended_post_type(
        'pcc-person',
        [
            'menu_icon' => 'dashicons-businessperson',
            'show_in_rest' => true,
        ],
        [
            'singular' => __('Person', 'platformcoop-support'),
            'plural' => __('People', 'platformcoop-support'),
            'slug' => 'people'
        ]
    );
}

/**
 * Retrieves an array of people, sorted by name.
 *
 * @return array
 */
function get_people()
{
    $people = get_posts([
        'post_type' => 'pcc-person',
        'orderby' => 'post_title',
        'order' => 'asc',
        'posts_per_page' => -1,
    ]);

    $options = [];

    foreach ($people as $person) {
        $options[ $person->ID ] = $person->post_title;
    }

    return $options;
}
