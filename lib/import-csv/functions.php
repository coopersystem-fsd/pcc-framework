<?php

namespace PCCFramework\Import_Users_Csv\Functions;


/**
 * Reads a CSV file and creates a new user and includes the user in an event.
 *
 * @return void
 */
function read_csv()
{

    if (isset($_POST['importcsv'])) :

        $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

        if (!empty($_FILES['import_file']['name']) && $extension == 'csv') :

            $errors = [];
            $warnings = [];
            $success = 0;

            $csv_file = fopen($_FILES['import_file']['tmp_name'], 'r');

            $delimiter = detect_delimiter($_FILES['import_file']['tmp_name']);

            $csv_headers = fgetcsv($csv_file, 0, $delimiter);

            $email_index = array_search('email', $csv_headers);

            if ($email_index === false) {
                return __('Column "email" not found!', 'pcc-framework');
            }

            $username_index = array_search('username', $csv_headers);
            $fist_name_index = array_search('fist_name', $csv_headers);
            $last_name_index = array_search('last_name', $csv_headers);
            $event_id_index = array_search('event_id', $csv_headers);

            while (($csv_data = fgetcsv($csv_file, 0, $delimiter)) !== FALSE) :
                $csv_data = array_map("utf8_encode", $csv_data);

                $email = trim($csv_data[$email_index]);
                $event_id = $event_id_index ? trim($csv_data[$event_id_index]) : null;
                $event_id = is_valid_event($event_id);
                $is_valid_event_id = $event_id && $event_id > 0;
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = __("Email address '$email' is invalid.", 'pcc-framework');
                    continue;
                }

                if ($user_id = email_exists($email)) {
                    if ($is_valid_event_id) {
                        $event_ids = get_user_meta($user_id, 'event_ids', true);

                        if (in_array($event_id, $event_ids)) {
                            $warnings[] = __("'$email' already registered in event ID $event_id", 'pcc-framework');
                            continue;
                        }

                        $user = get_user_by('email', $email);
                        $event_ids[] = $event_id;
                        update_user_meta($user_id, 'event_ids', $event_ids);
                        send_email_user_event($email, $user->user_login, $event_id);
                        $success++;
                        continue;
                    }

                    $errors[] = __("'$email' was not associated with event ID ($event_id) because the ID is not valid", 'pcc-framework');
                    continue;
                }

                $password = wp_generate_password();
                $username = $username_index && !empty(trim($csv_data[$username_index])) ? trim($csv_data[$username_index]) : strstr($email, '@', true);
                $user_id = wp_insert_user([
                    'user_pass' => $password,
                    'user_login' => !username_exists($username) ? $username : $username . substr(md5(microtime()),rand(0,26),5),
                    'user_email' => $email,
                    'first_name' => $fist_name_index ? trim($csv_data[$fist_name_index]) : '',
                    'last_name' => $last_name_index ? trim($csv_data[$last_name_index]) : '',
                    'show_admin_bar_front' => false,
                    'role' => 'external_user',
                    'meta_input' => $is_valid_event_id ? ['event_ids' => [$event_id]] : null,
                ]);

                if ($user_id > 0) {
                    send_email_user_created($email, $username, $password);

                    if ($is_valid_event_id) {
                        send_email_user_event($email, $username, $event_id);
                        $success++;
                        continue;
                    }

                    $warnings[] = __("The user '$email' was created but was not associated with event ID ($event_id) because the ID is not valid", 'pcc-framework');
                    continue;
                }

                $errors[] = __("Error creating user '$email'", 'pcc-framework');

            endwhile;

            return [
                'errors' => $errors,
                'warnings' => $warnings,
                'success' => $success,
            ];

        endif;
    endif;
}


/**
 * Detect CSV delimiter
 *
 * @param string $csv_file
 * @return string
 */
function detect_delimiter($csv_file)
{
    $delimiters = [';' => 0, ',' => 0, '\t' => 0, '|' => 0];

    $handle = fopen($csv_file, 'r');
    $firstLine = fgets($handle);
    fclose($handle); 
    foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($firstLine, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
}


/**
 * Sends an email with the user's credentials after being created.
 *
 * @param string $user_email
 * @param string $username
 * @param string $password
 * @return void
 */
function send_email_user_created($user_email, $username, $password)
{
    $subject = !empty(get_option('user_csv_new_user_subject')) ? get_option('user_csv_new_user_subject') : get_default_text('user_subject');
    $message = !empty(get_option('user_csv_new_user_message')) ? get_option('user_csv_new_user_message') : get_default_text('user_message');

    $subject = wp_unslash($subject);
    $message = removeslashes($message);

    $subject = str_replace("{{username}}", $username, $subject);
    $subject = str_replace("{{user_email}}", $user_email, $subject);

    $message = str_replace("{{username}}", $username, $message);
    $message = str_replace("{{password}}", $password, $message);
    $message = str_replace("{{user_email}}", $user_email, $message);

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail(
        $user_email,
        $subject,
        $message,
        $headers
    );
}


/**
 * Sends an email with information about the event in which the user was entered.
 *
 * @param string $user_email
 * @param string $username
 * @param int $event_id
 * @return void
 */
function send_email_user_event($user_email, $username, $event_id)
{
    $subject = !empty(get_option('user_csv_new_event_subject')) ? get_option('user_csv_new_event_subject') : get_default_text('event_subject');
    $message = !empty(get_option('user_csv_new_event_message')) ? get_option('user_csv_new_event_message') : get_default_text('event_message');

    $subject = wp_unslash($subject);
    $message = removeslashes($message);

    $event_title = get_the_title($event_id);
    $event_url = get_permalink($event_id);

    $subject = str_replace("{{username}}", $username, $subject);
    $subject = str_replace("{{event_name}}", $event_title, $subject);
    $subject = str_replace("{{user_email}}", $user_email, $subject);
    $subject = str_replace("{{event_url}}", $event_url, $subject);

    $message = str_replace("{{username}}", $username, $message);
    $message = str_replace("{{event_name}}", $event_title, $message);
    $message = str_replace("{{user_email}}", $user_email, $message);
    $message = str_replace("{{event_url}}", $event_url, $message);

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail(
        $user_email,
        $subject,
        $message,
        $headers
    );
}


/**
 * Format results after reading and completion of CSV file import and returns an HTML string.
 *
 * @param array $results
 * @return string
 */
function format_results($results)
{
    $html = '';

    if ($results['success'] > 0) {
        $html .= "<p style=\"color: #398f39;\"><b>" . __('Success:', 'pcc-framework') . "</b> " . $results['success'] . __(' users added/updated', 'pcc-framework') . "</p>";
    }
    if ($results['warnings']) {
        $html .= "<p style=\"color: #fda500;\"><b>" . __('Warnings:', 'pcc-framework') . "</b></p>";
        foreach ($results['warnings'] as $warning) {
            $html .= "<p style=\"color: #fda500;\">$warning</p>";
        }
    }
    if ($results['errors']) {
        $html .= "<p style=\"color: #eb2e2e;\"><b>" . __('Errors:', 'pcc-framework') . "</b></p>";
        foreach ($results['errors'] as $error) {
            $html .= "<p style=\"color: #eb2e2e;\">$error</p>";
        }
    }

    return $html;
}


/**
 * Save email message settings.
 *
 * @return void
 */
function save_settings()
{
    if (isset($_POST['save-user-csv-settings'])) {
        $new_user_subject = isset($_POST['email-new-user-subject']) ? $_POST['email-new-user-subject'] : '';
        $new_user_message = isset($_POST['email-new-user-message']) ? $_POST['email-new-user-message'] : '';
        $event_subject = isset($_POST['email-event-subject']) ? $_POST['email-event-subject'] : '';
        $event_message = isset($_POST['email-event-message']) ? $_POST['email-event-message'] : '';

        update_option('user_csv_new_user_subject', $new_user_subject);
        update_option('user_csv_new_user_message', $new_user_message);
        update_option('user_csv_new_event_subject', $event_subject);
        update_option('user_csv_new_event_message', $event_message);
    }
}


/**
 * Checks if the event ID is valid, it can be the Post ID or a unique value created on 
 * the event page in the Open Collective event ID. It returns the Post ID or false.
 *
 * @param int|string $event_id
 * @return int!false
 */
function is_valid_event($event_id)
{
    if (!$event_id) return false;

    if (!is_numeric($event_id)) {
        $args = array(
            'meta_key' => 'pcc_event_oc_id',
            'meta_value' => $event_id,
            'post_type' => 'pcc-event',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $event_ids = get_posts($args);
        wp_reset_postdata();

        return $event_ids ? $event_ids[0] : false;
    }
    var_dump($event_id);
    return get_post_type($event_id) === 'pcc-event' ? $event_id : false;
}



/**
 * Returns the default messages for sending emails
 *
 * @param string $type user_subject, event_subject, user_message or event_message depending 
 * on whether the email is user creation or user inclusion in a certain event.
 * @return string
 */
function get_default_text($type)
{
    if ($type === 'user_subject') {
        return __("Welcome", 'pcc-framework');
    }

    if ($type === 'event_subject') {
        return __("Now you can access the \"{{event_name}}\" event content", 'pcc-framework');
    }

    if ($type === 'user_message') {
        return __("
            <p>To log in, simply use this information:</p>
            <table>
                <tr>
                    <td>Username: </td>
                    <td>{{username}}</td>
                </tr>
                <tr>
                    <td>Password: </td>
                    <td>{{password}}</td>
                </tr>
            </table>", 'pcc-framework');
    }

    if ($type === 'event_message') {
        return __("
        <p>You can now access the event <a href=\"{{event_url}}\">\"{{event_name}}\"<\a></p>", 'pcc-framework');
    }
}



/**
 * Removes extra backslashes from email messages.
 *
 * @param string $string
 * @return string
 */
function removeslashes($string)
{
    $string = implode("", explode("\\", $string));
    return stripslashes(trim($string));
}


/**
 * Sends email to the user if he is manually added to an event through the user profile.
 *
 * @link https://developer.wordpress.org/reference/hooks/update_meta_type_metadata/
 * @param null|bool $check
 * @param int $user_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @return bool
 */
add_filter('update_user_metadata', function ($check, $user_id, $meta_key, $meta_value) {

    if ($meta_key == 'event_ids') {
        $current_user_events = get_user_meta($user_id, 'event_ids', true) ? get_user_meta($user_id, 'event_ids', true) : [];
        $new_events = array_diff($meta_value, $current_user_events);

        if (count($new_events) > 0) {
            $user = get_user_by('ID', $user_id);

            foreach ($new_events as $event_id) {
                send_email_user_event($user->user_email, $user->user_login, $event_id);
            }
        }
    }
    return $check;
}, 10, 4);
