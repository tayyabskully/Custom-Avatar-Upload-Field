// Add a custom field to user profiles
add_action('show_user_profile', 'custom_user_avatar_field');
add_action('edit_user_profile', 'custom_user_avatar_field');

function custom_user_avatar_field($user) {
    ?>
    <h3><?php _e("Custom Avatar", "my_domain"); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="custom_avatar"><?php _e("Custom Avatar URL"); ?></label></th>
            <td>
                <input type="text" name="custom_avatar" id="custom_avatar" value="<?php echo esc_attr(get_user_meta($user->ID, 'custom_avatar', true)); ?>" class="regular-text" />
                <br />
                <span class="description"><?php _e("Please enter the URL of your custom avatar image."); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

// Save the custom field
add_action('personal_options_update', 'save_custom_avatar_field');
add_action('edit_user_profile_update', 'save_custom_avatar_field');

function save_custom_avatar_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    // Validate and sanitize the input
    if (isset($_POST['custom_avatar'])) {
        $custom_avatar_url = esc_url_raw($_POST['custom_avatar']);
        update_user_meta($user_id, 'custom_avatar', $custom_avatar_url);
    }
}

// Display custom avatar instead of the default
add_filter('get_avatar', 'custom_avatar', 10, 5);

function custom_avatar($avatar, $id_or_email, $size, $default, $alt) {
    $user_id = null;

    // Determine the user ID from the input
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif (is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $user_id = $id_or_email->user_id;
        }
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if ($user) {
            $user_id = $user->ID;
        }
    }

    // Check for the custom avatar
    if ($user_id) {
        $custom_avatar_url = get_user_meta($user_id, 'custom_avatar', true);
        if (!empty($custom_avatar_url) && filter_var($custom_avatar_url, FILTER_VALIDATE_URL)) {
            return '<img alt="' . esc_attr($alt) . '" src="' . esc_url($custom_avatar_url) . '" class="avatar avatar-' . (int) $size . ' photo" height="' . (int) $size . '" width="' . (int) $size . '" />';
        }
    }

    // Return the default avatar if no custom avatar is found
    return $avatar;
}

// Display custom avatar anywhere on the site
function display_custom_avatar($user_id) {
    $custom_avatar_url = get_user_meta($user_id, 'custom_avatar', true);
    if (!empty($custom_avatar_url)) {
        echo '<img src="' . esc_url($custom_avatar_url) . '" alt="Custom Avatar" />';
    }
}
