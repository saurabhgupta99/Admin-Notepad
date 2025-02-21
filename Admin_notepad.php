<?php
/*
Plugin Name: Admin Notepad
Plugin URI: https://yourwebsite.com
Description: A simple notepad for admins that saves automatically.
Version: 1.0
Author: Saurabh
Author URI: https://saurabhkg.netlify.app/
License: GPL2
*/

if (!defined('ABSPATH')) exit; // Prevent direct access

// Add Notepad to Admin Menu
function admin_notepad_menu() {
    add_menu_page('Admin Notepad', 'Admin Notepad', 'manage_options', 'admin-notepad', 'admin_notepad_page', 'dashicons-edit', 100);
}
add_action('admin_menu', 'admin_notepad_menu');

// Notepad Page Content
function admin_notepad_page() {
    $notes = get_option('admin_notepad_content', '');
    echo '<div class="wrap"><h1>Admin Notepad</h1><textarea id="admin_notepad" style="width:100%;height:300px;">' . esc_textarea($notes) . '</textarea><p>Notes are saved automatically.</p></div>';
    admin_notepad_script();
}

// Add Notepad Widget
function admin_notepad_dashboard_widget() {
    wp_add_dashboard_widget('admin_notepad', 'Admin Notepad', function() {
        $notes = get_option('admin_notepad_content', '');
        echo '<textarea id="admin_notepad_dashboard" style="width:100%;height:150px;">' . esc_textarea($notes) . '</textarea>';
        admin_notepad_script();
    });
}
add_action('wp_dashboard_setup', 'admin_notepad_dashboard_widget');

// Save Notes via AJAX
function save_admin_notepad() {
    if (isset($_POST['notes'])) {
        update_option('admin_notepad_content', sanitize_textarea_field($_POST['notes']));
    }
    wp_die();
}
add_action('wp_ajax_save_admin_notepad', 'save_admin_notepad');

// JavaScript for Auto-Save
function admin_notepad_script() {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let textarea = document.querySelector("#admin_notepad, #admin_notepad_dashboard");
            if (!textarea) return;
            textarea.addEventListener('input', function() {
                fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "action=save_admin_notepad&notes=" + encodeURIComponent(this.value)
                });
            });
        });
    </script>
    <?php
}
