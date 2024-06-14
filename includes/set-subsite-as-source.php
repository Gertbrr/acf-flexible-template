<?php
// Hook into the network admin menu to add our menu item
add_action('network_admin_menu', 'acf_flexible_templates_network_menu');

function acf_flexible_templates_network_menu() {
    add_menu_page(
        'ACF Flexible Templates',  // Page title
        'ACF Flexible Templates',  // Menu title
        'manage_network_options',  // Capability
        'acf-flexible-templates',  // Menu slug
        'acf_flexible_templates_settings_page'  // Callback function
    );
}

// Display the settings page
function acf_flexible_templates_settings_page() {
    if (!current_user_can('manage_network_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('ACF Flexible Templates Settings', 'acf-flexible-templates'); ?></h1>
		
		<p>Kies hier een subsite die als bron gebruikt wordt om de templates op te halen.</p>
        <form method="post" action="edit.php?action=acf_flexible_templates_save_network_settings">
            <?php
            wp_nonce_field('acf_flexible_templates_save_network_settings');
            $selected_subsite = get_site_option('acf_flexible_templates_selected_subsite');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Selecteer subsite', 'acf-flexible-templates'); ?></th>
                    <td>
                        <select name="acf_flexible_templates_selected_subsite">
                            <?php
                            $sites = get_sites();
                            foreach ($sites as $site) {
                                $blog_details = get_blog_details($site->blog_id);
                                $selected = ($site->blog_id == $selected_subsite) ? 'selected="selected"' : '';
                                echo '<option value="' . esc_attr($site->blog_id) . '" ' . $selected . '>' . esc_html($blog_details->blogname) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Save the settings when the form is submitted
add_action('network_admin_edit_acf_flexible_templates_save_network_settings', 'acf_flexible_templates_save_network_settings');

function acf_flexible_templates_save_network_settings() {
    if (!current_user_can('manage_network_options') || !check_admin_referer('acf_flexible_templates_save_network_settings')) {
        wp_die(__('You do not have sufficient permissions to perform this action.'));
    }

    if (isset($_POST['acf_flexible_templates_selected_subsite'])) {
        $selected_subsite = intval($_POST['acf_flexible_templates_selected_subsite']);
        update_site_option('acf_flexible_templates_selected_subsite', $selected_subsite);
    }

    wp_redirect(add_query_arg(array('page' => 'acf-flexible-templates', 'updated' => 'true'), network_admin_url('admin.php')));
    exit;
}

// Function to retrieve the selected subsite
function acf_flexible_templates_get_selected_subsite() {
    return get_site_option('acf_flexible_templates_selected_subsite');
}
