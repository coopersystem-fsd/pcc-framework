<?php

namespace PCCFramework\Import_Users_Csv\Page;

use function PCCFramework\Import_Users_Csv\Functions\get_default_text;


/**
 * Add a subpage to the users page.
 *
 * @link https://developer.wordpress.org/reference/functions/add_submenu_page/
 * @return void
 */
function init()
{
    add_submenu_page(
        'users.php',
        __('Import users from CSV', 'pcc-framework'),
        __('Import users CSV', 'pcc-framework'),
        'create_users',
        'platformcoop_import_user',
        '\\PCCFramework\\Import_Users_Csv\\Page\\content_page'
    );
}


/**
 * Callback with subpage content for importing CSV files and email message settings.
 *
 * @return void
 */
function content_page()
{
    if (!current_user_can('create_users')) {
        return;
    }

    $default_tab = null;
    $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab; ?>

    <div class="wrap">
        <h1><?php _e('Import users from CSV', 'pcc-framework'); ?></h1>
        <nav class="nav-tab-wrapper">
            <a href="?page=platformcoop_import_user" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>"><?php _e('Import file', 'pcc-framework'); ?></a>
            <a href="?page=platformcoop_import_user&tab=settings" class="nav-tab <?php if ($tab === 'settings') : ?>nav-tab-active<?php endif; ?>"><?php _e('E-mail settings', 'pcc-framework'); ?></a>
        </nav>
        <div class="tab-content">
            <?php switch ($tab):
                case 'settings':
                    settings_tab();
                    break;

                default:
                    import_file_tab();
                    break;
            endswitch; ?>
        </div>
    <?php
}


/**
 * HTML for CSV file import tab.
 *
 * @return void
 */
function import_file_tab()
{ ?>
        <div class="card">
            <h2><?php _e('Instructions', 'pcc-framework'); ?></h2>
            <p><?php _e('Allowed columns: <b>email</b>, <b>event_id</b>, <b>username</b>, <b>first_name</b>, <b>last_name</b>.', 'pcc-framework'); ?></p>
            <p><?php _e('The file <b>MUST</b> have <b>email</b> column for importing users.', 'pcc-framework'); ?></p>
            <p><?php _e('If the <b>event_id</b> column does not exist or if the user does not have an event id in the file, the user will be created but not linked to the event.', 'pcc-framework'); ?></p>
            <p><?php _e('The columns <b>username</b>, <b>first_name</b>, <b>last_name</b> are optional.', 'pcc-framework'); ?></p>
            <p><?php _e('If the <b>username</b> column does not exist, the username will be generated from the email.', 'pcc-framework'); ?></p>
            <form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
                <p><input type="file" name="import_file"></p>
                <p class="submit"><input type="submit" name="importcsv" id="importcsv" class="button button-secondary" value="Load File"></p>
            </form>
            <?php $results = \PCCFramework\Import_Users_Csv\Functions\read_csv(); ?>
            <?php if ($results) : ?>
                <h2><?php _e('Status', 'pcc-framework'); ?></h2>
                <?= \PCCFramework\Import_Users_Csv\Functions\format_results($results); ?>
            <?php endif; ?>
        </div>
    <?php
}


/**
 * HTML for e-mail settings tab.
 *
 * @return void
 */
function settings_tab()
{
    $new_user_subject = !empty(get_option('user_csv_new_user_subject')) ? get_option('user_csv_new_user_subject') : get_default_text('user_subject');
    $new_user_message = !empty(get_option('user_csv_new_user_message')) ? get_option('user_csv_new_user_message') : get_default_text('user_message');
    $event_subject = !empty(get_option('user_csv_new_event_subject')) ? get_option('user_csv_new_event_subject') : get_default_text('event_subject');
    $event_message = !empty(get_option('user_csv_new_event_message')) ? get_option('user_csv_new_event_message') : get_default_text('event_message');
    ?>

        <form id="user-csv-settings" method="post" action="">
            <h2><?php _e('User created', 'pcc-framework'); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="email-new-user-subject"><?php _e('Subject', 'pcc-framework'); ?></label></th>
                        <td>
                            <input id="email-new-user-subject" type="text" name="email-new-user-subject" autocomplete="off" value="<?= stripslashes(htmlentities($new_user_subject)); ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="email-new-user-message"><?php _e('Message', 'pcc-framework'); ?></label></th>
                        <td>
                            <textarea id="email-new-user-message" name="email-new-user-message" class="large-text" rows="10" autocomplete="off"><?= \PCCFramework\Import_Users_Csv\Functions\removeslashes($new_user_message); ?></textarea>
                            <p class="description"><?php _e('Use <b>{{username}}</b> to indicate username', 'pcc-framework'); ?></p>
                            <p class="description"><?php _e('Use <b>{{password}}</b> to indicate the user\'s password', 'pcc-framework'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3><?php _e('User added to an event', 'pcc-framework'); ?></h3>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="email-event-subject"><?php _e('Subject', 'pcc-framework'); ?></label></th>
                        <td>
                            <input id="email-event-subject" type="text" name="email-event-subject" autocomplete="off" value="<?= stripslashes(htmlentities($event_subject)); ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="email-event-message"><?php _e('Message', 'pcc-framework'); ?></label></th>
                        <td>
                            <textarea id="email-event-message" name="email-event-message" class="large-text" rows="10" autocomplete="off"><?= \PCCFramework\Import_Users_Csv\Functions\removeslashes($event_message); ?></textarea>
                            <p class="description"><?php _e('Use <b>{{username}}</b> to indicate username', 'pcc-framework'); ?></p>
                            <p class="description"><?php _e('Use <b>{{event_name}}</b> to indicate the event name', 'pcc-framework'); ?></p>
                            <p class="description"><?php _e('Use <b>{{event_url}}</b> to indicate the event URL', 'pcc-framework'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" name="save-user-csv-settings" id="save-user-csv-settings" class="button button-primary" value="Save settings"></p>
        </form>
    <?php
}
