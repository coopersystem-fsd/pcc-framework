<?php

namespace PCCFramework\Import_Users_Csv\Metabox;

function user_settings()
{
    $cmb = new_cmb2_box([
        'id' => 'platformcoop_user_event_info',
        'title' => __('User events', 'pcc-framework'),
        'object_types' => ['user'],
        'option_key' => 'platformcoop_user_event_info',
        'capability' => 'edit_users',
    ]);
    $cmb->add_field([
        'name' => __('Event list', 'pcc-framework'),
        'id' => 'event_ids',
        'type' => 'multicheck',
        'options_cb' => '\\PCCFramework\\Import_Users_Csv\\Metabox\\user_event_list',
        'column' => array(
            'position' => 5,
            'name'     => 'Events',
        ),
        'display_cb' => '\\PCCFramework\\Import_Users_Csv\\Metabox\\user_event_list_column'
    ]);
}


function user_event_list($query_args)
{

    $args = [
        'post_type' => 'pcc-event',
        'numberposts' => -1
    ];

    $posts = query_posts($args);

    $post_options = array();
    if ($posts) {
        foreach ($posts as $post) {
            $post_options[$post->ID] = $post->post_title;
        }
    }

    wp_reset_query();

    return $post_options;
}


function user_event_list_column($field_args, $field)
{
    if ($field->escaped_value() && is_array($field->escaped_value())) :
        foreach ($field->escaped_value() as $event_id) : ?>
            <p><a href="<?= get_permalink($event_id); ?>"><?= get_the_title($event_id); ?><a></p>
        <?php
        endforeach;
    endif;
}



