<?php

namespace PCCFramework\Import_Users_Csv\Metabox;


/**
 * Add event metabox in user profile and user event column in users table.
 *
 * @return void
 */
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


/**
 * Custom callback to return event list in user profile.
 *
 * @param array $query_args
 * @return array
 */
function user_event_list($query_args)
{

    $args = array(
        'meta_key' => 'pcc_event_oc_paid_event',
        'meta_value' => 'on',
        'post_type' => 'pcc-event',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $posts = get_posts($args);
    
    $post_options = array();
    if ($posts) {
        foreach ($posts as $post) {
            $post_options[$post->ID] = $post->post_title;
        }
    }

    wp_reset_postdata();

    return $post_options;
}


/**
 * Custom callback to display user events in the users table.
 *
 * @param object $field_args
 * @param object $field
 * @return void
 */
function user_event_list_column($field_args, $field)
{
    if ($field->escaped_value() && is_array($field->escaped_value())) :
        foreach ($field->escaped_value() as $event_id) : ?>
            <p><a href="<?= get_permalink($event_id); ?>"><?= get_the_title($event_id); ?><a></p>
        <?php
        endforeach;
    endif;
}



