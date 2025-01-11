<?php
/*
Plugin Name: GDPR/CCPA Gizlilik Bildirimi
Plugin URI: https://github.com/hltyvz
Description: Tarayıcı diline göre dinamik çerez bildirimi.
Version: 1.7
Author: Halit Yavuz
Author URI: https://github.com/hltyvz
License: GPL2
*/

// Pass admin settings to JavaScript
function gdpr_cookie_notice_enqueue_scripts() {
    wp_enqueue_style('gdpr-cookie-notice-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('gdpr-cookie-notice-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);

    // Yönetici ayarlarını JavaScript'e ilet
    $custom_settings = array(
        'bg_color' => get_option('gdpr_cookie_notice_bg_color', '#1A314F'),
        'text_color' => get_option('gdpr_cookie_notice_text_color', '#ffffff'),
        'button_bg_color' => get_option('gdpr_cookie_notice_button_bg_color', '#ffffff'),
        'button_text_color' => get_option('gdpr_cookie_notice_button_text_color', '#1A314F'),
        'button_hover_bg_color' => get_option('gdpr_cookie_notice_button_hover_bg_color', '#bbc2cb'),
        'message_tr' => get_option('gdpr_cookie_notice_message_tr', 'Bu site çerezler kullanıyor. Daha fazla bilgi için gizlilik politikamıza göz atın.'),
        'message_en' => get_option('gdpr_cookie_notice_message_en', 'This site uses cookies. Please see our privacy policy for more information.'),
        'policy_url_tr' => get_permalink(get_option('gdpr_cookie_notice_page_id_tr', 0)),
        'policy_url_en' => get_permalink(get_option('gdpr_cookie_notice_page_id_en', 0)),
    );

    wp_localize_script('gdpr-cookie-notice-script', 'gdprCookieSettings', $custom_settings);
}
add_action('wp_enqueue_scripts', 'gdpr_cookie_notice_enqueue_scripts');

// Create the admin panel menu
function gdpr_cookie_notice_admin_menu() {
    add_menu_page(
        'Çerez Bildirimi Ayarları',
        'Çerez Bildirimi',
        'manage_options',
        'gdpr-cookie-notice',
        'gdpr_cookie_notice_settings_page',
        'dashicons-shield-alt',
        81
    );
}
add_action('admin_menu', 'gdpr_cookie_notice_admin_menu');

// Create admin panel page
function gdpr_cookie_notice_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

// Save settings
    if (isset($_POST['gdpr_cookie_notice_save'])) {
        update_option('gdpr_cookie_notice_page_id_tr', intval($_POST['gdpr_cookie_notice_page_id_tr']));
        update_option('gdpr_cookie_notice_page_id_en', intval($_POST['gdpr_cookie_notice_page_id_en']));
        update_option('gdpr_cookie_notice_message_tr', sanitize_text_field($_POST['gdpr_cookie_notice_message_tr']));
        update_option('gdpr_cookie_notice_message_en', sanitize_text_field($_POST['gdpr_cookie_notice_message_en']));
        update_option('gdpr_cookie_notice_bg_color', sanitize_hex_color($_POST['gdpr_cookie_notice_bg_color']));
        update_option('gdpr_cookie_notice_text_color', sanitize_hex_color($_POST['gdpr_cookie_notice_text_color']));
        update_option('gdpr_cookie_notice_button_bg_color', sanitize_hex_color($_POST['gdpr_cookie_notice_button_bg_color']));
        update_option('gdpr_cookie_notice_button_text_color', sanitize_hex_color($_POST['gdpr_cookie_notice_button_text_color']));
        update_option('gdpr_cookie_notice_button_hover_bg_color', sanitize_hex_color($_POST['gdpr_cookie_notice_button_hover_bg_color']));
        echo '<div class="updated"><p>Ayarlar kaydedildi.</p></div>';
    }

// Get current settings
    $selected_page_id_tr = get_option('gdpr_cookie_notice_page_id_tr', 0);
    $selected_page_id_en = get_option('gdpr_cookie_notice_page_id_en', 0);
    $pages = get_pages();

    $message_tr = get_option('gdpr_cookie_notice_message_tr', 'Bu site çerezler kullanıyor. Daha fazla bilgi için gizlilik politikamıza göz atın.');
    $message_en = get_option('gdpr_cookie_notice_message_en', 'This site uses cookies. Please see our privacy policy for more information.');

    echo '<div class="wrap">
        <h1>Çerez Bildirimi Ayarları</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_page_id_tr">Gizlilik Politikası Sayfası (Türkçe)</label></th>
                    <td>
                        <select id="gdpr_cookie_notice_page_id_tr" name="gdpr_cookie_notice_page_id_tr">';
                            foreach ($pages as $page) {
                                $selected = $selected_page_id_tr == $page->ID ? 'selected' : '';
                                echo '<option value="' . $page->ID . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                            }
                        echo '</select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_page_id_en">Privacy Policy Page (English)</label></th>
                    <td>
                        <select id="gdpr_cookie_notice_page_id_en" name="gdpr_cookie_notice_page_id_en">';
                            foreach ($pages as $page) {
                                $selected = $selected_page_id_en == $page->ID ? 'selected' : '';
                                echo '<option value="' . $page->ID . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                            }
                        echo '</select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_message_tr">Çerez Bildirimi Mesajı (Türkçe)</label></th>
                    <td><textarea id="gdpr_cookie_notice_message_tr" name="gdpr_cookie_notice_message_tr" rows="3" cols="50">' . esc_textarea($message_tr) . '</textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_message_en">Cookie Notice Message (English)</label></th>
                    <td><textarea id="gdpr_cookie_notice_message_en" name="gdpr_cookie_notice_message_en" rows="3" cols="50">' . esc_textarea($message_en) . '</textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_bg_color">Arka Plan Rengi</label></th>
                    <td><input type="color" id="gdpr_cookie_notice_bg_color" name="gdpr_cookie_notice_bg_color" value="' . esc_attr(get_option('gdpr_cookie_notice_bg_color', '#1A314F')) . '"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_text_color">Metin Rengi</label></th>
                    <td><input type="color" id="gdpr_cookie_notice_text_color" name="gdpr_cookie_notice_text_color" value="' . esc_attr(get_option('gdpr_cookie_notice_text_color', '#ffffff')) . '"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_button_bg_color">Buton Arka Plan Rengi</label></th>
                    <td><input type="color" id="gdpr_cookie_notice_button_bg_color" name="gdpr_cookie_notice_button_bg_color" value="' . esc_attr(get_option('gdpr_cookie_notice_button_bg_color', '#ffffff')) . '"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_button_text_color">Buton Metin Rengi</label></th>
                    <td><input type="color" id="gdpr_cookie_notice_button_text_color" name="gdpr_cookie_notice_button_text_color" value="' . esc_attr(get_option('gdpr_cookie_notice_button_text_color', '#1A314F')) . '"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gdpr_cookie_notice_button_hover_bg_color">Buton Hover Arka Plan Rengi</label></th>
                    <td><input type="color" id="gdpr_cookie_notice_button_hover_bg_color" name="gdpr_cookie_notice_button_hover_bg_color" value="' . esc_attr(get_option('gdpr_cookie_notice_button_hover_bg_color', '#bbc2cb')) . '"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="gdpr_cookie_notice_save" id="submit" class="button button-primary" value="Kaydet">
            </p>
        </form>
    </div>';
}

// Print cookie notification to the screen
function gdpr_cookie_notice_display() {
    echo '<div id="gdpr-cookie-notice" data-message-tr="' . esc_attr(get_option('gdpr_cookie_notice_message_tr', 'Bu site çerezler kullanıyor. Daha fazla bilgi için gizlilik politikamıza göz atın.')) . '"
        data-message-en="' . esc_attr(get_option('gdpr_cookie_notice_message_en', 'This site uses cookies. Please see our privacy policy for more information.')) . '"
        data-url-tr="' . esc_url(get_permalink(get_option('gdpr_cookie_notice_page_id_tr', 0))) . '"
        data-url-en="' . esc_url(get_permalink(get_option('gdpr_cookie_notice_page_id_en', 0))) . '"></div>';
}
add_action('wp_footer', 'gdpr_cookie_notice_display');
