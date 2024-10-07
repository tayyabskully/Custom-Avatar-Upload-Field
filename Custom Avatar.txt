// Add custom avatar URL field in user profile
function custom_user_avatar_field($profile_fields) {
    $profile_fields['custom_avatar_url'] = 'Custom Avatar URL';
    return $profile_fields;
}
add_filter('user_contactmethods', 'custom_user_avatar_field');

// Display avatar URL input field in user profile
function display_avatar_url_input($user) { ?>
    <h3>Custom Avatar</h3>
    <table class="form-table">
        <tr>
            <th><label for="custom_avatar_url">Avatar URL</label></th>
            <td>
                <input type="text" name="custom_avatar_url" id="custom_avatar_url" value="<?php echo esc_url(get_user_meta($user->ID, 'custom_avatar_url', true)); ?>" /><br />
                <span class="description">Enter the URL of your custom avatar image.</span>
                <?php 
                $custom_avatar_url = get_user_meta($user->ID, 'custom_avatar_url', true);
                if ($custom_avatar_url) : ?>
                    <br /><img src="<?php echo esc_url($custom_avatar_url); ?>" width="100" />
                <?php endif; ?>
            </td>
        </tr>
    </table>
<?php }
add_action('show_user_profile', 'display_avatar_url_input');
add_action('edit_user_profile', 'display_avatar_url_input');

// Save custom avatar URL
function save_custom_avatar_url($user_id) {
    if (isset($_POST['custom_avatar_url'])) {
        $custom_avatar_url = esc_url_raw($_POST['custom_avatar_url']);
        update_user_meta($user_id, 'custom_avatar_url', $custom_avatar_url);
    }
}
add_action('personal_options_update', 'save_custom_avatar_url');
add_action('edit_user_profile_update', 'save_custom_avatar_url');

// Replace default avatar with custom avatar URL
function custom_avatar($avatar, $id_or_email, $size, $default, $alt) {
    $user = false;

    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', $id_or_email);
    } elseif (is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user && is_object($user)) {
        $custom_avatar_url = get_user_meta($user->ID, 'custom_avatar_url', true);

        if ($custom_avatar_url) {
            $avatar = '<img src="' . esc_url($custom_avatar_url) . '" alt="' . esc_attr($alt) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" class="avatar avatar-' . esc_attr($size) . ' photo" />';
        }
    }

    return $avatar;
}
add_filter('get_avatar', 'custom_avatar', 10, 5);
