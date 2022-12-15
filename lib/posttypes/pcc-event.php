<?php

namespace PCCFramework\PostTypes\Event;

use CommerceGuys\Addressing\Country\CountryRepository;
use function PCCFramework\PostTypes\Person\get_people;

/**
 * Registers the `pcc-event` post type.
 *
 * @return null
 */
function init()
{
    register_extended_post_type(
        'pcc-event',
        [
            'has_archive' => false,
            'hierarchical' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'menu_position' => 24,
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'page-attributes', 'custom-fields', 'thumbnail'],
        ],
        [
            'singular' => __('Event', 'pcc-framework'),
            'plural' => __('Events', 'pcc-framework'),
            'slug' => 'events'
        ]
    );
}

/**
 * Registers meta fields for the `pcc-event` post type.
 *
 * @return null
 */
function register_meta()
{
    register_post_meta('pcc-event', 'pcc_event_start', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
    ]);
    register_post_meta('pcc-event', 'pcc_event_end', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
    ]);
    register_post_meta('pcc-event', 'pcc_event_venue', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ]);
    register_post_meta('pcc-event', 'pcc_event_venue_address', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ]);
    register_post_meta('pcc-event', 'pcc_event_registration_url', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ]);
    register_post_meta('pcc-event', 'pcc_event_type', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ]);
}

/**
 * Registers the Event Data metabox and meta fields.
 *
 * @return null
 */
function data()
{
    $prefix = 'pcc_event_';
    $countryRepository = new CountryRepository();
    $countries = [];
    foreach ($countryRepository->getAll() as $country) {
        $countries[$country->getCountryCode()] = $country->getName();
    }

    $languages = [
        'en'=> 'English',
        'pt'=> 'Portuguese',
        'es'=> 'Spanish',
        'it'=> 'Italian',
        'tr'=> 'Turkish',
        'ch'=> 'Chinese',
        'th'=> 'Thai',
    ];

    $cmb = new_cmb2_box([
        'id'            => 'event_data',
        'title'         => __('Event Data', 'pcc-framework'),
        'object_types'  => ['pcc-event'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
    ]);

    $cmb->add_field([
        'name' => __('Start', 'pcc-framework'),
        'id' => $prefix . 'start',
        'type' => 'text_datetime_timestamp',
        'description' =>
        __('The date and time at which the event begins.', 'pcc-framework'),
    ]);

    $cmb->add_field([
        'name' => __('End', 'pcc-framework'),
        'id' => $prefix . 'end',
        'type' => 'text_datetime_timestamp',
        'description' =>
        __('The date and time at which the event ends.', 'pcc-framework'),
    ]);

    $cmb->add_field([
        'name' => __('Time zone', 'pcc-framework'),
        'id' => $prefix . 'timezone',
        'type' => 'select',
        'options' => array(
            'EST' => __('EST', 'pcc-framework'),
            'EDT' => __('EDT', 'pcc-framework'),
        ),
    ]);

    $cmb->add_field([
        'name' => __('Format', 'pcc-framework'),
        'id' => $prefix . 'format',
        'type' => 'radio',
        'default' => 'not_online',
        'options' => array(
            'not_online' => __('Face-to-face', 'pcc-framework'),
            'async' => __('Online Asynchronous', 'pcc-framework'),
            'sync' => __('Online Synchronous', 'pcc-framework'),
            'async_sync' => __('Online Asynchronous and Synchronous', 'pcc-framework'),
        ),
    ]);

    $cmb->add_field([
        'name' => __('Language', 'pcc-framework'),
        'id' => $prefix . 'language',
        'type' => 'select',
        'default' => 'en',
        'options' => $languages,
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => wp_json_encode(array('async', 'sync', 'async_sync')),
        ),
    ]);

    $cmb->add_field([
        'name' => __('Second language (optional)', 'pcc-framework'),
        'id' => $prefix . 'second_language',
        'type' => 'select',
        'default' => 'none',
        'options' => array_merge(['none' => 'None'], $languages),
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
        'description' =>
        __('(Live translation)', 'pcc-framework'),
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => wp_json_encode(array('async', 'sync', 'async_sync')),
        ),
    ]);

    $cmb->add_field([
        'name' => __('Certificate course', 'pcc-framework'),
        'id' => $prefix . 'certificate',
        'type' => 'checkbox',
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => wp_json_encode(array('async', 'sync', 'async_sync')),
        ),
    ]);

    $cmb->add_field([
        'name' => __('Venue Name', 'pcc-framework'),
        'id'   => $prefix . 'venue',
        'type' => 'textarea_small',
        'show_on_cb' => 'PCCFramework\PostTypes\Event\not_parent_online_format',
        'description' =>
        __('The name of the event&rsquo;s principal venue.', 'pcc-framework'),
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => 'not_online',
        ),
    ]);

    $cmb->add_field([
        'name' => __('Venue Street Address', 'pcc-framework'),
        'id'   => $prefix . 'venue_street_address',
        'type' => 'text',
        'show_on_cb' => 'PCCFramework\PostTypes\Event\not_parent_online_format',
        'description' =>
        __('The street address of the event&rsquo;s principal venue.', 'pcc-framework'),
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => 'not_online',
        ),
    ]);

    $cmb->add_field([
        'name' => __('Venue Town/City', 'pcc-framework'),
        'id'   => $prefix . 'venue_locality',
        'type' => 'text',
        'show_on_cb' => 'PCCFramework\PostTypes\Event\not_parent_online_format',
        'description' =>
        __('The town or city of the event&rsquo;s principal venue.', 'pcc-framework'),
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => 'not_online',
        ),
    ]);

    $cmb->add_field([
        'name' => __('Venue Region', 'pcc-framework'),
        'id'   => $prefix . 'venue_region',
        'type' => 'text',
        'show_on_cb' => 'PCCFramework\PostTypes\Event\not_parent_online_format',
        'description' =>
        __('The province, state, or region of the event&rsquo;s principal venue.', 'pcc-framework'),
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => 'not_online',
        ),
    ]);

    $cmb->add_field([
        'name' => __('Venue Postal Code', 'pcc-framework'),
        'id'   => $prefix . 'venue_postal_code',
        'type' => 'text',
        'show_on_cb' => 'PCCFramework\PostTypes\Event\not_parent_online_format',
        'description' =>
        __('The postal code of the event&rsquo;s principal venue.', 'pcc-framework'),
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => 'not_online',
        ),
    ]);

    $cmb->add_field([
        'name' => __('Venue Country', 'pcc-framework'),
        'id'   => $prefix . 'venue_country',
        'type' => 'select',
        'default' => 'US',
        'options' => $countries,
        'show_on_cb' => 'PCCFramework\PostTypes\Event\not_parent_online_format',
        'description' =>
        __('The country of the event&rsquo;s principal venue.', 'pcc-framework'),
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'format',
            'data-conditional-value'  => 'not_online',
        ),
    ]);

    $cmb->add_field([
        'name' => __('Registration Link', 'pcc-framework'),
        'id'   => $prefix . 'registration_url',
        'type' => 'text_url',
        'protocols' => ['http', 'https'],
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
        'description' =>
        __('A hyperlink to the event&rsquo;s external registration page.', 'pcc-framework'),
    ]);

    $cmb->add_field([
        'name' => __('Event Type', 'pcc-framework'),
        'id'   => $prefix . 'type',
        'type' => 'select',
        'show_option_none' => false,
        'default' => 'pcc',
        'options' => [
            'community' => __('Community Event', 'pcc-framework'),
            'conference' => __('PCC Conference', 'pcc-framework'),
            'pcc' => __('PCC Event', 'pcc-framework'),
            'icde' => __('ICDE Event', 'pcc-framework'),
            'course' => __('Course', 'pcc-framework'),
            'past_course' => __('Past Course', 'pcc-framework'),
        ],
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
        'description' =>
        __('The type of event.', 'pcc-framework'),
    ]);

    $cmb->add_field([
        'name' => __('Price', 'pcc-framework'),
        'id'   => $prefix . 'price',
        'type' => 'text',
        'show_option_none' => false,
        'default' => '',
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
        'description' =>
        __('The price of event.', 'pcc-framework'),
    ]);

    $cmb->add_field([
        'name' => __('Instructor/Coach', 'pcc-framework'),
        'id' => $prefix . 'instructor_type',
        'type' => 'select',
        'options' => array(
            'instructor' => __('Instructor', 'pcc-framework'),
            'coach' => __('Coach', 'pcc-framework'),
        ),
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
        'attributes'    => array(
            'data-conditional-id'     => $prefix . 'type',
            'data-conditional-value'  => wp_json_encode(array('course', 'past_course')),
        ),
    ]);

    $cmb->add_field([
        'name'    => __('Event Banner Video', 'pcc-framework'),
        'desc'    => 'Upload an animated banner video for this event.',
        'id'      => $prefix . 'banner_video',
        'type'    => 'file',
        'options' => [
            'url' => false, // Hide the text input for the url
        ],
        'query_args' => [
            'type' => 'video/mp4',
        ],
    ]);

    $cmb->add_field([
        'name' => __('Participants', 'pcc-framework'),
        'desc' =>
        'Participants will be shown alphabetically on the participants page.',
        'id'   => $prefix . 'participants',
        'type' => 'select',
        'show_option_none' => true,
        'options' => get_people(),
        'repeatable' => true,
        'text' => [
            'add_row_text' => __('Add Participant', 'pcc-framework'),
        ]
    ]);

    $cmb->add_field([
        'name' => __('Featured Participants', 'pcc-framework'),
        'desc' =>
        'Featured participants will be shown in this order on the main event page.',
        'id'   => $prefix . 'featured_participants',
        'type' => 'select',
        'show_option_none' => true,
        'options' => get_people(),
        'repeatable' => true,
        'text' => [
            'add_row_text' => __('Add Featured Participant', 'pcc-framework'),
        ]
    ]);

    $cmb_oc = new_cmb2_box([
        'id'            => 'event_oc',
        'title'         => __('Open Collective', 'pcc-framework'),
        'object_types'  => ['pcc-event'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
        'show_on_cb' => 'PCCFramework\PostTypes\Event\is_parent_event',
    ]);

    $cmb_oc->add_field([
        'name' => __('Event URL', 'pcc-framework'),
        'id' => $prefix . 'oc_event_link',
        'type' => 'text',
        'description' =>
        __('In order for the link to be displayed correctly as an embed link, it must obey the following structure: <br />"https://opencollective.com/{{colective-name}}/events/{{event-name-abcd01234}}/contribute/{{tier-name-00000}}"<br />Ex.: https://opencollective.com/platform-cooperativism-pcc/events/platform-coops-event/contribute/general-access-ticket-98765', 'pcc-framework'),
    ]);

    $cmb_oc->add_field( array(
        'id'   => $prefix . 'oc_event_embed_link',
        'type' => 'hidden',
    ));

    $cmb_oc->add_field([
        'name' => __('Open Collective event ID', 'pcc-framework'),
        'id' => $prefix . 'oc_event_id',
        'type' => 'text_medium',
        'description' =>
        __('You can leave it blank if you want to use the Wordpress Post ID.<br/> (This ID is the same one used to import the list of users through the CSV file).', 'pcc-framework'),
    ]);

}

/**
 * Registers the Event Sponsors metabox and meta fields.
 *
 * @return null
 */
function sponsors()
{
    $prefix = 'pcc_event_';

    $cmb = new_cmb2_box([
        'id'            => 'event_sponsors',
        'title'         => __('Event Sponsors', 'pcc-framework'),
        'object_types'  => ['pcc-event'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
        'show_on_cb'    => 'PCCFramework\PostTypes\Event\is_parent_event'
    ]);

    $sponsor_id = $cmb->add_field([
        'id' => $prefix . 'sponsors',
        'type' => 'group',
        'options' => [
            'group_title' => __('Sponsor {#}', 'pcc-framework'),
            'add_button' => __('Add Sponsor', 'pcc-framework'),
            'remove_button' => __('Remove Sponsor', 'pcc-framework'),
            'sortable' => true,
        ],
    ]);

    $cmb->add_group_field($sponsor_id, [
        'name' => __('Sponsor Name', 'pcc-framework'),
        'id'   => 'name',
        'type' => 'text',
    ]);

    $cmb->add_group_field($sponsor_id, [
        'name' => __('Sponsor Link', 'pcc-framework'),
        'id' => 'link',
        'type' => 'text_url',
        'protocols' => ['http', 'https'],
    ]);

    $cmb->add_group_field($sponsor_id, [
        'name' => __('Sponsor Logo', 'pcc-framework'),
        'id' => 'logo',
        'type'    => 'file',
        'options' => [
            'url' => false,
        ],
        'text' => [
            'add_upload_file_text' => __('Add/Upload Logo', 'pcc-framework')
        ],
        'query_args' => [
            'type' => [
                'image/jpeg',
                'image/png',
            ]
        ],
        'preview_size' => 'medium',
    ]);
}


/**
 * Registers the Event Classes metabox and meta fields.
 *
 * @return null
 */
function classes()
{
    $prefix = 'pcc_event_';

    $cmb = new_cmb2_box([
        'id'            => 'event_classes',
        'title'         => __('Course classes', 'pcc-framework'),
        'object_types'  => ['pcc-event'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
        'show_on_cb'    => 'PCCFramework\PostTypes\Event\is_parent_event'
    ]);

    $class_id = $cmb->add_field([
        'id' => $prefix . 'classes',
        'type' => 'group',
        'options' => [
            'group_title' => __('Class {#}', 'pcc-framework'),
            'add_button' => __('Add Class', 'pcc-framework'),
            'remove_button' => __('Remove Class', 'pcc-framework'),
            'sortable' => true,
        ],
    ]);

    $cmb->add_group_field($class_id, [
        'name' => __('Class title', 'pcc-framework'),
        'id'   => 'title',
        'type' => 'text',
    ]);

    $cmb->add_group_field($class_id, [
        'name' => __('Date', 'pcc-framework'),
        'id' => 'date',
        'type' => 'text_date',
    ]);

    $cmb->add_group_field($class_id, [
        'name' => __('Start time', 'pcc-framework'),
        'id' => 'start_time',
        'type' => 'text_time',
    ]);

    $cmb->add_group_field($class_id, [
        'name' => __('End time', 'pcc-framework'),
        'id' => 'end_time',
        'type' => 'text_time',
    ]);

    $cmb->add_group_field($class_id, [
        'name' => __('Instructor', 'pcc-framework'),
        'id'   => 'instructor',
        'type' => 'select',
        'show_option_none' => true,
        'options' => get_people(),
    ]);

    $cmb->add_group_field($class_id, [
        'name' => __('Topics', 'pcc-framework'),
        'id'   => 'topics',
        'type' => 'text',
        'repeatable' => true,
        'text' => [
            'add_row_text' => __('Add topic', 'pcc-framework'),
        ]
    ]);
}

/**
 * Determine if event is a parent or a child (for CMB2's `show_on` callback).
 *
 * @param mixed $cmb The CMB2 meta box.
 *
 * @return bool
 */
function is_parent_event($cmb)
{
    return empty(get_post_ancestors($cmb->object_id));
}

/**
 * Determine if event is a child and if parent is an online format (for CMB2's `show_on` callback).
 *
 * @param mixed $cmb The CMB2 meta box.
 *
 * @return bool
 */
function not_parent_online_format($cmb)
{
    $event_formats = ['async', 'sync','async_sync'];
    $event_parents = get_post_ancestors($cmb->object_id);

    if (!empty($event_parents)) {
        $parent_id = array_pop($event_parents);
        $parent_event_format = get_post_meta($parent_id, 'pcc_event_format', true);
        return !in_array($parent_event_format, $event_formats);
    }
    return true;
}
