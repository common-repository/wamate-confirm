<?php
/**
 * Plugin Name: WaMate Confirm - Order Confirmation
 * Description: Automatically confirm orders via WhatsApp when received.
 * Version: 1.7.0
 * Author: WaMate
 * Author URI: https://wamate.online
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add WaMate top-level menu and subpages
add_action('admin_menu', 'wamate_plugin_menu');
function wamate_plugin_menu() {
    add_menu_page('WaMate', 'WaMate', 'manage_options', 'wamate_settings', 'wamate_settings_page');
    add_submenu_page('wamate_settings', 'Settings', 'Settings', 'manage_options', 'wamate_settings', 'wamate_settings_page');
    add_submenu_page('wamate_settings', 'Log', 'Log', 'manage_options', 'wamate_log', 'wamate_log_page');
}

// Settings page content
function wamate_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verify nonce for form submission
    if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wamate_settings-options')) {
        // Process form data here
    }

    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
/* General settings page styles */
    .wrap {
        background-color: #e5e5e5;
        border-radius: 10px;
        padding: 20px;
        margin: 20px auto;
        max-width: 800px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    /* Settings section header */
    .wrap h1 {
        color: #25D366;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    /* Description text */
    .wrap p {
        font-size: 16px;
        color: #4c4c4c;
        text-align: center;
    }
    /* Settings table */
    form .form-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px;
    }
    /* Settings table rows */
    form .form-table th,
    form .form-table td {
        padding: 15px;
        vertical-align: middle;
    }
    /* Settings table headers */
    form .form-table th {
        background-color: #25D366;
        color: #ffffff;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: bold;
        text-align: left;
        border-radius: 10px 0 0 10px;
        position: relative;
        padding-left: 50px;
    }
    form .form-table th::before {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
    }
    /* Settings table inputs */
    form .form-table td {
        background-color: #ffffff;
        border-radius: 0 10px 10px 0;
    }
    form .form-table input[type="text"],
    form .form-table input[type="number"],
    form .form-table textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    /* Submit button */
    form .submit input[type="submit"] {
        background-color: #25D366;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: block;
        margin: 20px auto;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    form .submit input[type="submit"]:hover {
        background-color: #1DA851;
    }
    /* Icon styles */
    .icon-message::before {
        content: "\f075"; /* Speech bubble icon */
    }
    .icon-country::before {
        content: "\f0ac"; /* Globe icon */
    }
    .icon-instance::before {
        content: "\f121"; /* Code icon */
    }
    .icon-token::before {
        content: "\f023"; /* Lock icon */
    }
    .icon-phone::before {
        content: "\f095"; /* Phone icon */
    }
    </style>
    <div class="wrap">
        <h1><?php echo esc_html__('WaMate Settings', 'wamate'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wamate_settings');
            wp_nonce_field('wamate_settings-options');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row" class="icon-message"><?php echo esc_html__('Custom Message', 'wamate'); ?></th>
                    <td>
                        <textarea id="wamate_order_notification_message" name="wamate_order_notification_message" rows="5" cols="50"><?php echo esc_textarea(get_option('wamate_order_notification_message', '')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="icon-country"><?php echo esc_html__('Country Code', 'wamate'); ?></th>
                    <td>
                        <input type="text" id="wamate_order_notification_country_code" name="wamate_order_notification_country_code" value="<?php echo esc_attr(get_option('wamate_order_notification_country_code', '')); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="icon-instance"><?php echo esc_html__('Instance ID', 'wamate'); ?></th>
                    <td>
                        <input type="text" id="wamate_order_notification_instance_id" name="wamate_order_notification_instance_id" value="<?php echo esc_attr(get_option('wamate_order_notification_instance_id', '')); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="icon-token"><?php echo esc_html__('Access Token', 'wamate'); ?></th>
                    <td>
                        <input type="text" id="wamate_order_notification_access_token" name="wamate_order_notification_access_token" value="<?php echo esc_attr(get_option('wamate_order_notification_access_token', '')); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="icon-phone"><?php echo esc_html__('Phone Number Length', 'wamate'); ?></th>
                    <td>
                        <input type="number" id="wamate_order_notification_phone_number_length" name="wamate_order_notification_phone_number_length" value="<?php echo esc_attr(get_option('wamate_order_notification_phone_number_length', '10')); ?>" min="1" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
// Log page content
// Log page content
function wamate_log_page() {
    ?>
    <div class="wrap">
        <h2>WaMate Log</h2>
        <form method="post">
            <input type="hidden" name="clear_log_nonce" value="<?php echo wp_create_nonce( 'clear_log_nonce' ); ?>">
            <button type="submit" name="clear_log" class="button button-secondary">Clear Log</button>
        </form>
        <?php
        // Check if the clear log button is clicked
        if (isset($_POST['clear_log']) && wp_verify_nonce($_POST['clear_log_nonce'], 'clear_log_nonce')) {
            // Clear the log entries
            delete_option('wamate_log_entries');
            echo '<div class="notice notice-success"><p>Log cleared successfully.</p></div>';
        }
        
        // Get the log entries
        $log_entries = get_option('wamate_log_entries', array());
        
        // Display log entries from newest to oldest
        if (!empty($log_entries)) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Status</th><th>Billing Number</th><th>Message</th><th>Response</th><th>Resend</th></tr></thead>';
            echo '<tbody>';
            // Loop through log entries in reverse order
            $log_entries = array_reverse($log_entries);
            foreach ($log_entries as $log_entry) {
                // Decode the log entry
                $entry = json_decode($log_entry, true);
                // Extract relevant information
                $status = isset($entry['status']) ? $entry['status'] : '';
                $billing_number = isset($entry['billing_number']) ? $entry['billing_number'] : '';
                $message = isset($entry['message']) ? $entry['message'] : '';
                $response = isset($entry['api_response']) ? json_decode($entry['api_response'], true) : null;
                $response_message = is_array($response) ? json_encode($response) : $response; // Handle array response
                // Display log entry
                echo '<tr>';
                echo '<td>' . esc_html($status) . '</td>';
                echo '<td>' . esc_html($billing_number) . '</td>';
                echo '<td>' . esc_html($message) . '</td>';
                echo '<td>' . esc_html($response_message) . '</td>';
                echo '<td><button class="button button-primary" onclick="resendMessage(\'' . esc_js($billing_number) . '\', \'' . esc_js($message) . '\')">Resend</button></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No log entries found.</p>';
        }
        ?>
    </div>
    <?php
}
function wamate_enqueue_custom_admin_style($hook) {
    if (strpos($hook, 'wamate') === false) {
        return;
    }
    wp_enqueue_style('wamate_custom_admin_css', plugin_dir_url(__FILE__) . 'wamate-admin-style.css');
}
// Add settings fields to the WaMate settings page
add_action('admin_init', 'wamate_plugin_settings');
function wamate_plugin_settings() {
    register_setting('wamate_settings', 'wamate_order_notification_message');
    register_setting('wamate_settings', 'wamate_order_notification_country_code');
    register_setting('wamate_settings', 'wamate_order_notification_instance_id');
    register_setting('wamate_settings', 'wamate_order_notification_access_token');
    register_setting('wamate_settings', 'wamate_order_notification_phone_number_length');

    add_settings_section('wamate_order_notification_settings_section', '', 'wamate_order_notification_settings_section_callback', 'wamate_settings');

    add_settings_field('wamate_order_notification_message', 'Custom Message', 'wamate_order_notification_message_callback', 'wamate_settings', 'wamate_order_notification_settings_section');
    add_settings_field('wamate_order_notification_country_code', 'Country Code', 'wamate_order_notification_country_code_callback', 'wamate_settings', 'wamate_order_notification_settings_section');
    add_settings_field('wamate_order_notification_instance_id', 'Instance ID', 'wamate_order_notification_instance_id_callback', 'wamate_settings', 'wamate_order_notification_settings_section');
    add_settings_field('wamate_order_notification_access_token', 'Access Token', 'wamate_order_notification_access_token_callback', 'wamate_settings', 'wamate_order_notification_settings_section');
    add_settings_field('wamate_order_notification_phone_number_length', 'Phone Number Length', 'wamate_order_notification_phone_number_length_callback', 'wamate_settings', 'wamate_order_notification_settings_section');
}

// Settings section callback
function wamate_order_notification_settings_section_callback() {
    echo '<div class="wrap"><h1>WaMate Settings</h1><p>Enter your settings below:</p></div>';
}
// Settings field callbacks
function wamate_order_notification_message_callback() {
    $value = get_option('wamate_order_notification_message', '');
    echo '<textarea id="wamate_order_notification_message" name="wamate_order_notification_message" rows="5" cols="50">' . esc_attr($value) . '</textarea>';
}

function wamate_order_notification_country_code_callback() {
    $value = get_option('wamate_order_notification_country_code', '');
    echo '<input type="text" id="wamate_order_notification_country_code" name="wamate_order_notification_country_code" value="' . esc_attr($value) . '" />';
}

function wamate_order_notification_instance_id_callback() {
    $value = get_option('wamate_order_notification_instance_id', '');
    echo '<input type="text" id="wamate_order_notification_instance_id" name="wamate_order_notification_instance_id" value="' . esc_attr($value) . '" class="regular-text" placeholder="Enter instance IDs, separated by commas" />';
    echo '<p class="description">Enter instance IDs, separated by commas.</p>';
}

function wamate_get_next_instance_id() {
    $instance_ids = get_option('wamate_order_notification_instance_id', '');
    $instance_ids = array_filter(array_map('trim', explode(',', $instance_ids)));
    
    if (empty($instance_ids)) {
        return '';
    }

    $current_index = get_option('wamate_current_instance_id_index', 0);
    $next_index = ($current_index + 1) % count($instance_ids);
    
    update_option('wamate_current_instance_id_index', $next_index);
    
    return $instance_ids[$next_index];
}

function wamate_sanitize_instance_ids($input) {
    $ids = array_map('trim', explode(',', $input));
    $sanitized_ids = array_map('sanitize_text_field', $ids);
    return implode(', ', array_filter($sanitized_ids));
}
register_setting('wamate_settings', 'wamate_order_notification_instance_id', 'wamate_sanitize_instance_ids');

function wamate_order_notification_access_token_callback() {
    $value = get_option('wamate_order_notification_access_token', '');
    echo '<input type="text" id="wamate_order_notification_access_token" name="wamate_order_notification_access_token" value="' . esc_attr($value) . '" />';
}

function wamate_order_notification_phone_number_length_callback() {
    $value = get_option('wamate_order_notification_phone_number_length', 10);
    echo '<input type="number" id="wamate_order_notification_phone_number_length" name="wamate_order_notification_phone_number_length" value="' . esc_attr($value) . '" min="1" />';
}

//hooks
add_action('woocommerce_checkout_order_processed', 'wamate_custom_order_notification_send_notification', 10, 1);
function wamate_custom_order_notification_send_notification($order_id) {
    // Check if the custom order meta exists
    $api_triggered = get_post_meta($order_id, '_wamate_api_triggered', true);

    // If API has not been triggered in the first run, proceed
    if (!$api_triggered) {
        // Get the order object
        $order = wc_get_order($order_id);

        if (!$order) {
            error_log("WaMate: Unable to get order object for order ID: " . $order_id);
            return;
        }

        // Get the billing phone number and remove spaces and +
        $billing_phone = $order->get_billing_phone();
        $billing_phone = str_replace([' ', '+'], '', $billing_phone);

        // Get the desired phone number length from settings
        $phone_number_length = (int) get_option('wamate_order_notification_phone_number_length', 10);

        // Make sure the number is the desired length
        if (strlen($billing_phone) > $phone_number_length) {
            $billing_phone = substr($billing_phone, -1 * $phone_number_length);
        } elseif (strlen($billing_phone) < $phone_number_length) {
            // If the number is less than the desired length, pad it with leading zeros
            $billing_phone = str_pad($billing_phone, $phone_number_length, '0', STR_PAD_LEFT);
        }

        // Use the configured country code if available, otherwise default to '1'
        $country_code = get_option('wamate_order_notification_country_code', '1');
        
        // Get other configuration options
		$instance_id = wamate_get_next_instance_id();
        $access_token = get_option('wamate_order_notification_access_token', '');
        
        // Sanitize input
        $billing_phone = sanitize_text_field($billing_phone);
        $country_code = sanitize_text_field($country_code);
        $instance_id = sanitize_text_field($instance_id);
        $access_token = sanitize_text_field($access_token);
        
        // Get the custom message
        $message_template = get_option('wamate_order_notification_message', 'Default Message');

        // Include total price if configured
        if (get_option('wamate_order_notification_include_total_price')) {
            $total_price = html_entity_decode(strip_tags(wc_price($order->get_total())), ENT_QUOTES, 'UTF-8');
        }

        // Get delivery cost and order total
        $delivery_cost = html_entity_decode(strip_tags(wc_price($order->get_shipping_total())), ENT_QUOTES, 'UTF-8');
        $order_total = html_entity_decode(strip_tags(wc_price($order->get_total())), ENT_QUOTES, 'UTF-8');
        
        // Get customer's name, country, state, and address
        $customer_name = $order->get_billing_first_name();
        $customer_country_code = $order->get_billing_country();
        $customer_state_code = $order->get_billing_state();
        $customer_address = $order->get_billing_address_1();

        // Convert country and state codes to full names
        $customer_country = WC()->countries->countries[$customer_country_code];
        $customer_state = WC()->countries->get_states($customer_country_code)[$customer_state_code];

        // Get order details
        $order_details = get_order_details($order);

        // Replace shortcodes in the message
        $message = str_replace('[name]', $customer_name, $message_template);
        $message = str_replace('[orderid]', $order_id, $message);
        $message = str_replace('[dlv]', $delivery_cost, $message);
        $message = str_replace('[cost]', $order_total, $message);
        $message = str_replace('[orderdetails]', $order_details, $message);
        $message = str_replace('[country]', $customer_country, $message);
        $message = str_replace('[state]', $customer_state, $message);
        $message = str_replace('[address]', $customer_address, $message);

        // Decode URL-encoded characters
        $message = urldecode($message);

        // Build the API URL
        $api_url = "https://dash.wamate.online/api/send?number={$country_code}{$billing_phone}&type=text&message=" . urlencode($message) . "&instance_id={$instance_id}&access_token={$access_token}";

        // Send the request to the API
        $response = wp_remote_get($api_url);

        // Save the API response to log
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            // Response was successful, parse the response body
            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body, true);

            // Check if the response indicates success
            if (isset($response_data['status']) && $response_data['status'] === 'success') {
                // Log success
                $log_entry = array(
                    'status' => 'success',
                    'billing_number' => $billing_phone,
                    'message' => $message,
                    'api_response' => $response_body
                );
            } else {
                // Log API error
                $log_entry = array(
                    'status' => 'failure',
                    'billing_number' => $billing_phone,
                    'message' => $message,
                    'api_response' => $response_body
                );
            }
        } else {
            // Log HTTP request error
            $error_message = is_wp_error($response) ? $response->get_error_message() : 'Unknown error';
            $log_entry = array(
                'status' => 'failure',
                'billing_number' => $billing_phone,
                'message' => $message,
                'api_response' => $error_message
            );
        }

        // Log the entry
        $log_entries = get_option('wamate_log_entries', array());
        $log_entries[] = json_encode($log_entry);
        update_option('wamate_log_entries', $log_entries);

        // Create custom order meta to mark that the API has been triggered
        update_post_meta($order_id, '_wamate_api_triggered', true);
    }
}

// Function to get order details
function get_order_details($order) {
    $items = $order->get_items();
    $order_details = "\n";
    $added_products = [];
    foreach ($items as $item) {
        $product_name = html_entity_decode(strip_tags($item->get_name()), ENT_QUOTES, 'UTF-8');
        
        if (!in_array($product_name, $added_products)) {
            $order_details .= "- " . $product_name;

            // Check if the item is a variation
            if ($item->get_variation_id()) {
                $product_variation = wc_get_product($item->get_variation_id());
                if ($product_variation) {
                    $variation_attributes = $product_variation->get_variation_attributes();
                    if (!empty($variation_attributes)) {
                        $variation_details = [];
                        foreach ($variation_attributes as $attribute => $value) {
                            $taxonomy = str_replace('attribute_', '', $attribute);
                            $term = get_term_by('slug', $value, $taxonomy);
                            $attribute_value = $term ? $term->name : $value;
                            $variation_details[] = html_entity_decode(strip_tags($attribute_value), ENT_QUOTES, 'UTF-8');
                        }
                        if (!empty($variation_details)) {
                            $order_details .= " - " . implode(', ', $variation_details);
                        }
                    }
                }
            }
            
            $quantity = $item->get_quantity();
            $order_details .= " x {$quantity}\n";
            
        }
    }
    return $order_details;
}

// Add Remarketing submenu page
// Add Remarketing submenu
add_action('admin_menu', 'wamate_add_remarketing_submenu');
function wamate_add_remarketing_submenu() {
    add_submenu_page(
        'wamate_settings', // parent slug
        'Remarketing', // page title
        'Remarketing', // menu title
        'manage_options', // capability
        'wamate_remarketing', // menu slug
        'wamate_remarketing_page' // callback function
    );
}

// Remarketing page content
function wamate_remarketing_page() {
    ?>
<style>
/* General settings page styles */
    .wrap {
        background-color: #e5e5e5;
        border-radius: 10px;
        padding: 20px;
        margin: 20px auto;
        max-width: 800px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    /* Settings section header */
    .wrap h1 {
        color: #25D366;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    /* Description text */
    .wrap p {
        font-size: 16px;
        color: #4c4c4c;
        text-align: center;
    }
    /* Settings table */
    form .form-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px;
    }
    /* Settings table rows */
    form .form-table th,
    form .form-table td {
        padding: 15px;
        vertical-align: middle;
    }
    /* Settings table headers */
    form .form-table th {
        background-color: #25D366;
        color: #ffffff;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: bold;
        text-align: left;
        border-radius: 10px 0 0 10px;
        position: relative;
        padding-left: 50px;
    }
    form .form-table th::before {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
    }
    /* Settings table inputs */
    form .form-table td {
        background-color: #ffffff;
        border-radius: 0 10px 10px 0;
    }
    form .form-table input[type="text"],
    form .form-table input[type="number"],
    form .form-table textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    /* Submit button */
    form .submit input[type="submit"] {
        background-color: #25D366;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: block;
        margin: 20px auto;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    form .submit input[type="submit"]:hover {
        background-color: #1DA851;
    }
    /* Icon styles */
    .icon-message::before {
        content: "\f075"; /* Speech bubble icon */
    }
    .icon-country::before {
        content: "\f0ac"; /* Globe icon */
    }
    .icon-instance::before {
        content: "\f121"; /* Code icon */
    }
    .icon-token::before {
        content: "\f023"; /* Lock icon */
    }
    .icon-phone::before {
        content: "\f095"; /* Phone icon */
    }
    </style>
    <div class="wrap">
        <h2>Remarketing</h2>
        <form id="remarketing_form" method="post" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date"><br><br>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date"><br><br>
            <label for="status">Status:</label>
            <select id="status" name="status">
                <?php
                // Get all possible order statuses
                $order_statuses = wc_get_order_statuses();
                foreach ($order_statuses as $key => $value) {
                    echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
                }
                ?>
            </select><br><br>
            <input type="button" id="grab_numbers" value="Grab Numbers">
        </form>
        <br>
        <!-- Display phone numbers here -->
        <div id="phone_numbers"></div>
        <br>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#grab_numbers').click(function() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var status = $('#status').val();

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'grab_numbers_action',
                    start_date: start_date,
                    end_date: end_date,
                    status: status
                },
                success: function(response) {
                    $('#phone_numbers').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#send_message').click(function() {
            var customer_message = $('#customer_message').val();
            var phone_numbers = $('#phone_numbers textarea').val();

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'trigger_send_messages_action',
                    customer_message: customer_message,
                    phone_numbers: phone_numbers
                },
                success: function(response) {
                    alert(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
    </script>
    <?php
}

// AJAX callback to retrieve and display phone numbers
add_action('wp_ajax_grab_numbers_action', 'wamate_grab_numbers_ajax');
function wamate_grab_numbers_ajax() {
    // Log received data for debugging
    error_log(print_r($_POST, true));

    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $status = sanitize_text_field($_POST['status']);

    // Get country code and phone number length from settings
    $country_code = get_option('wamate_order_notification_country_code', '1');
    $phone_number_length = (int) get_option('wamate_order_notification_phone_number_length', 10);

    // Get orders within the specified date range and status
    $args = array(
        'date_query' => array(
            'after' => $start_date,
            'before' => $end_date,
            'inclusive' => true,
        ),
        'status' => $status,
        'limit' => -1, // Retrieve all orders
    );
    $orders = wc_get_orders($args);

    // Prepare phone numbers HTML
    $phone_numbers_html = '<textarea rows="10" cols="50">';
    foreach ($orders as $order) {
        $billing_phone = $order->get_billing_phone();
        // Remove any non-numeric characters
        $billing_phone = preg_replace('/\D/', '', $billing_phone);
        // Ensure phone number length matches the specified length
        $billing_phone = substr($billing_phone, -$phone_number_length);
        // Add country code if not present
        if (strpos($billing_phone, $country_code) !== 0) {
            $billing_phone = $country_code . $billing_phone;
        }
        // Add phone number to textarea if it matches the specified length
        if (strlen($billing_phone) === ($phone_number_length + strlen($country_code))) {
            $phone_numbers_html .= $billing_phone . "\n";
        }
    }
    $phone_numbers_html .= '</textarea>';

    // Return phone numbers HTML
    echo $phone_numbers_html;

    // Always exit to avoid extra output
    exit;
}
add_action('admin_menu', 'wamate_add_order_status_menu');
function wamate_add_order_status_menu() {
    add_submenu_page(
        'wamate_settings', // Parent slug
        'Order Status Notifications', // Page title
        'Order Status Notifications', // Menu title
        'manage_options', // Capability
        'wamate_order_status', // Menu slug
        'wamate_order_status_page' // Callback function
    );
}

function wamate_order_status_page() {
    ?>
<style>
/* General settings page styles */
    .wrap {
        background-color: #e5e5e5;
        border-radius: 10px;
        padding: 20px;
        margin: 20px auto;
        max-width: 800px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    /* Settings section header */
    .wrap h1 {
        color: #25D366;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    /* Description text */
    .wrap p {
        font-size: 16px;
        color: #4c4c4c;
        text-align: center;
    }
    /* Settings table */
    form .form-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px;
    }
    /* Settings table rows */
    form .form-table th,
    form .form-table td {
        padding: 15px;
        vertical-align: middle;
    }
    /* Settings table headers */
    form .form-table th {
        background-color: #25D366;
        color: #ffffff;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: bold;
        text-align: left;
        border-radius: 10px 0 0 10px;
        position: relative;
        padding-left: 50px;
    }
    form .form-table th::before {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
    }
    /* Settings table inputs */
    form .form-table td {
        background-color: #ffffff;
        border-radius: 0 10px 10px 0;
    }
    form .form-table input[type="text"],
    form .form-table input[type="number"],
    form .form-table textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    /* Submit button */
    form .submit input[type="submit"] {
        background-color: #25D366;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: block;
        margin: 20px auto;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    form .submit input[type="submit"]:hover {
        background-color: #1DA851;
    }
    /* Icon styles */
    .icon-message::before {
        content: "\f075"; /* Speech bubble icon */
    }
    .icon-country::before {
        content: "\f0ac"; /* Globe icon */
    }
    .icon-instance::before {
        content: "\f121"; /* Code icon */
    }
    .icon-token::before {
        content: "\f023"; /* Lock icon */
    }
    .icon-phone::before {
        content: "\f095"; /* Phone icon */
    }
    </style>
    <div class="wrap">
        <h2>Order Status Notifications</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('wamate_order_status_settings');
            do_settings_sections('wamate_order_status_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'wamate_register_order_status_settings');
function wamate_register_order_status_settings() {
    $order_statuses = wc_get_order_statuses();

    foreach ($order_statuses as $status_key => $status_label) {
        register_setting('wamate_order_status_settings', "wamate_notify_{$status_key}", 'absint');
        register_setting('wamate_order_status_settings', "wamate_message_{$status_key}", 'sanitize_textarea_field');

        add_settings_section(
            'wamate_order_status_settings_section',
            'Notification Settings for Order Statuses check which status to send whatsapp mesaagese to it use  [name], [orderid], and [status].to customize the messages',
            null,
            'wamate_order_status_settings'
        );

        add_settings_field(
            "wamate_notify_{$status_key}",
            "Notify on " . esc_html($status_label),
            function() use ($status_key) {
                $value = get_option("wamate_notify_{$status_key}", '0');
                echo '<input type="checkbox" id="wamate_notify_' . esc_attr($status_key) . '" name="wamate_notify_' . esc_attr($status_key) . '" value="1" ' . checked(1, $value, false) . ' />';
            },
            'wamate_order_status_settings',
            'wamate_order_status_settings_section'
        );

        add_settings_field(
            "wamate_message_{$status_key}",
            esc_html($status_label) . " Message",
            function() use ($status_key) {
                $value = get_option("wamate_message_{$status_key}", '');
                echo '<textarea id="wamate_message_' . esc_attr($status_key) . '" name="wamate_message_' . esc_attr($status_key) . '" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
            },
            'wamate_order_status_settings',
            'wamate_order_status_settings_section'
        );
    }
}

// Handle order status change
add_action('woocommerce_order_status_changed', 'wamate_order_status_changed', 10, 4);
function wamate_order_status_changed($order_id, $old_status, $new_status, $order) {
    $notify = get_option("wamate_notify_wc-{$new_status}", '0');
    if ($notify) {
        $message_template = get_option("wamate_message_wc-{$new_status}", '');
        
        $billing_phone = str_replace([' ', '+'], '', $order->get_billing_phone());
        $phone_number_length = (int) get_option('wamate_order_notification_phone_number_length', 10);
        if (strlen($billing_phone) > $phone_number_length) {
            $billing_phone = substr($billing_phone, -$phone_number_length);
        }
        $country_code = get_option('wamate_order_notification_country_code', '1');
        $instance_id = wamate_get_next_instance_id();
        $access_token = get_option('wamate_order_notification_access_token', '');
        
        $customer_name = $order->get_billing_first_name();
        $message = str_replace('[name]', $customer_name, $message_template);
        $message = str_replace('[orderid]', $order_id, $message);
        $message = str_replace('[status]', wc_get_order_status_name($new_status), $message);

        $api_url = "https://dash.wamate.online/api/send?number={$country_code}{$billing_phone}&type=text&message=" . urlencode($message) . "&instance_id={$instance_id}&access_token={$access_token}";

        $response = wp_remote_get($api_url);

        $log_entry = array(
            'status' => wp_remote_retrieve_response_code($response) === 200 ? 'success' : 'failure',
            'billing_number' => $billing_phone,
            'message' => $message,
            'api_response' => wp_remote_retrieve_body($response)
        );
        $log_entries = get_option('wamate_log_entries', array());
        $log_entries[] = json_encode($log_entry);
        update_option('wamate_log_entries', $log_entries);
    }
}
add_action('admin_menu', 'wamate_add_docs_menu');

function wamate_add_docs_menu() {
    add_submenu_page(
        'wamate_settings', // Parent slug
        'Documentation', // Page title
        'Docs', // Menu title
        'manage_options', // Capability
        'wamate_docs', // Menu slug
        'wamate_docs_page' // Callback function
    );
}

function wamate_docs_page() {
    ?>
    <div class="wrap">
        <h2>Wamate Plugin Documentation</h2>
        <div style="max-width: 800px; line-height: 1.6;">
            <h3>Setup Instructions</h3>
            <ol>
                <li><strong>Register an Account:</strong>
                    <ul>
                        <li>Go to <a href="https://dash.wamate.com" target="_blank">Wamate Dashboard</a>.</li>
                        <li>Register for a free account.</li>
                    </ul>
                </li>
                <li><strong>Add WhatsApp Account:</strong>
                    <ul>
                        <li>Navigate to <strong>WhatsApp</strong> in the dashboard.</li>
                        <li>Click on <strong>Add Account</strong>.</li>
                        <li>A QR code will appear.</li>
                    </ul>
                </li>
                <li><strong>Scan QR Code:</strong>
                    <ul>
                        <li>Open WhatsApp on your phone.</li>
                        <li>Scan the QR code.</li>
                        <li>Wait for the page to load fully.</li>
                    </ul>
                </li>
                <li><strong>Retrieve Instance ID and Access Token:</strong>
                    <ul>
                        <li>After scanning, obtain the <strong>Instance ID</strong> and <strong>Access Token</strong>.</li>
                    </ul>
                </li>
                <li><strong>Configure the Plugin in WordPress:</strong>
                    <ul>
                        <li>Go to <strong>Wamate</strong> in your WordPress dashboard.</li>
                        <li>Under <strong>Settings</strong>, enter your <strong>Instance ID( Multiple Insatnce comma seprated )</strong>, <strong>Access Token</strong>, and <strong>country code</strong>.</li>
                        <li>Specify the <strong>message</strong> template and how many characters the phone number should be.</li>
                    </ul>
                </li>
            </ol>
            <h3>Using Placeholders</h3>
			<h4>[status] works only in status notification ,[orderdetails] workds only instant notification </h4>
            <p>You can use placeholders in your messages to customize notifications:</p>
            <ul>
                <li><strong>[name]</strong>: Customer's name.</li>
                <li><strong>[orderid]</strong>: Order ID.</li>
                <li><strong>[cost]</strong>: Order total.</li>
				<li><strong>[orderdetails]</strong>: Order Details.</li>
                <li><strong>[status]</strong>: Order status.</li>
				<li><strong>[country]</strong>: Customer's Country.</li>
				<li><strong>[state]</strong>: Customer's state.</li>
				<li><strong>[address]</strong>: Customer's address.</li>
            </ul>
            <p>Example:<br>
            <code>"Hello [name], your order [orderid] totaling [cost] is now [status] order details [orderdetails]."</code></p>
            <h3>Order Status Notifications</h3>
            <p>Settings allow you to enable notifications for specific order statuses:</p>
            <ul>
                <li>Check the box next to each status to send a message when an order changes to that status.</li>
                <li>Customize the message for each status using the placeholders.</li>
            </ul>
            <h3>Log</h3>
            <p>The <strong>Log</strong> section provides details of all messages sent:</p>
            <ul>
                <li>View the date, order ID, status, and the message content.</li>
                <li>This helps in tracking and debugging any issues with notifications.</li>
            </ul>
            <h3>Additional Settings</h3>
            <ul>
                <li><strong>Country Code:</strong> Set the default country code for phone numbers.</li>
                <li><strong>Phone Number Length:</strong> Specify the expected length of phone numbers.</li>
            </ul>
            <p>Ensure that your messages comply with WhatsApp guidelines and contain only the permitted number of characters.</p>
        </div>
    </div>
    <?php
}

// Function to sanitize and normalize phone numbers
function antfkm_convert_arabic_indic_to_western_numerals($string) {
    $arabic_indic = ['۰', '۱', '۲', '۳', '٤', '۵', '٦', '۷', '۸', '۹'];
    $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    return str_replace($arabic_indic, $western, $string);
}

// Hook to convert phone number before checkout
add_action('woocommerce_before_checkout_process', 'antfkm_convert_phone_number_before_checkout');

function antfkm_convert_phone_number_before_checkout() {
    if (isset($_POST['billing_phone'])) {
        $_POST['billing_phone'] = antfkm_convert_arabic_indic_to_western_numerals($_POST['billing_phone']);
    }
}

// Optional: Add JavaScript to convert the number in real-time for display purposes
add_action('wp_footer', 'antfkm_add_phone_conversion_script');

function antfkm_add_phone_conversion_script() {
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#billing_phone').on('input', function() {
                var arabicIndicNumerals = ['۰', '۱', '۲', '۳', '٤', '۵', '٦', '۷', '۸', '۹'];
                var westernNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                var value = $(this).val();
                for (var i = 0; i < arabicIndicNumerals.length; i++) {
                    value = value.replace(new RegExp(arabicIndicNumerals[i], 'g'), westernNumerals[i]);
                }
                $(this).val(value);
            });
        });
        </script>
        <?php
    }
}

// Normalize the phone number entered during checkout
add_filter('woocommerce_checkout_posted_data', 'antfkm_convert_phone_number');

function antfkm_convert_phone_number($posted_data) {
    if (isset($posted_data['billing_phone'])) {
        $posted_data['billing_phone'] = antfkm_convert_arabic_indic_to_western_numerals($posted_data['billing_phone']);
    }
    return $posted_data;
}

// Normalize phone numbers for comparison
function antfkm_wc_phone_blocker_normalize_phone($phone) {
    return preg_replace('/\D/', '', $phone); // Removes non-digit characters
}

// Hook into WooCommerce checkout process to check phone numbers
function antfkm_wc_phone_blocker_check_number($data, $errors) {
    $blocked_numbers = get_option('wc_blocked_phone_numbers', []);

    // If no blocked numbers, do nothing
    if (empty($blocked_numbers)) {
        return;
    }

    // Normalize the phone number entered during checkout
    $entered_phone = antfkm_wc_phone_blocker_normalize_phone($data['billing_phone']);

    // Normalize each blocked number for comparison
    $normalized_blocked_numbers = array_map('antfkm_wc_phone_blocker_normalize_phone', $blocked_numbers);

    // Check if the normalized phone number is in the blocked list
    if (in_array($entered_phone, $normalized_blocked_numbers)) {
        // Increment the blocked attempt counter for this phone number
        $blocked_attempts = get_option('wc_blocked_phone_attempts', []);
        if (!isset($blocked_attempts[$entered_phone])) {
            $blocked_attempts[$entered_phone] = 0;
        }
        $blocked_attempts[$entered_phone]++;
        update_option('wc_blocked_phone_attempts', $blocked_attempts);

        // Add an error to prevent checkout
        $errors->add('blocked_phone', __('This phone number is not allowed to place orders.', 'woocommerce'));
    }
}
add_action('woocommerce_after_checkout_validation', 'antfkm_wc_phone_blocker_check_number', 10, 2);

// Add the settings page to WooCommerce menu
function antfkm_wc_phone_blocker_add_settings_page() {
    add_submenu_page(
        'wamate_settings',
        'Phone Blocker Settings',
        'Phone Blocker',
        'manage_options',
        'wc-phone-blocker',
        'antfkm_wc_phone_blocker_settings_page'
    );
}
add_action('admin_menu', 'antfkm_wc_phone_blocker_add_settings_page');

// Render the settings page
function antfkm_wc_phone_blocker_settings_page() {
    // Handle form submissions to add blocked numbers
    if (isset($_POST['add_number']) && !empty($_POST['phone_number'])) {
        $blocked_numbers = get_option('wc_blocked_phone_numbers', []);
        
        // Normalize the entered phone number
        $phone_number = antfkm_wc_phone_blocker_normalize_phone(sanitize_text_field($_POST['phone_number']));

        if (!in_array($phone_number, $blocked_numbers)) {
            $blocked_numbers[] = $phone_number;
            update_option('wc_blocked_phone_numbers', $blocked_numbers);
            echo '<div class="updated"><p>Phone number added successfully.</p></div>';
        } else {
            echo '<div class="error"><p>This phone number is already blocked.</p></div>';
        }
    }

    // Display Blocked Numbers and Their Blocked Attempts
    $blocked_numbers = get_option('wc_blocked_phone_numbers', []);
    $blocked_attempts = get_option('wc_blocked_phone_attempts', []);

    ?>
    <div class="wrap">
        <h1>Phone Blocker Settings</h1>

        <!-- Add Number Form -->
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="phone_number">Add Phone Number</label></th>
                    <td>
                        <input type="text" name="phone_number" class="regular-text" required>
                        <button type="submit" name="add_number" class="button button-primary">Add Number</button>
                    </td>
                </tr>
            </table>
        </form>

        <!-- Display Blocked Numbers and Their Blocked Attempts -->
        <h2>Blocked Numbers</h2>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>Phone Number</th>
                    <th>Blocked Attempts</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($blocked_numbers)) : ?>
                    <?php foreach ($blocked_numbers as $number) : ?>
                        <tr>
                            <td><?php echo esc_html($number); ?></td>
                            <td><?php echo isset($blocked_attempts[$number]) ? intval($blocked_attempts[$number]) : 0; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="2">No blocked numbers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
add_action('admin_menu', 'wamate_add_useful_plugins_submenu');

// Function to add the submenu under the desired plugin menu
function wamate_add_useful_plugins_submenu() {
    // Ensure the parent slug matches the main menu slug of the plugin you're targeting
    add_submenu_page(
        'wamate_settings', // Correct this slug to match the actual parent menu slug
        'Must-Have Plugins', // Page title
        'Must-Have Plugins', // Menu title
        'manage_options', // Capability required to access the submenu
        'Must-Have Plugins', // Menu slug
        'wamate_useful_plugins_page_callback' // Corrected callback function name
    );
}

// Callback function to display the Useful Plugins page content
function wamate_useful_plugins_page_callback() {
    ?>
    <div class="wrap">
        <div class="useful-plugins-container">
    <h1 style="
        font-family: 'Georgia', serif; 
        font-size: 32px; 
        font-weight: bold; 
        background: linear-gradient(to right, #b78628, #f1c40f); 
        -webkit-background-clip: text; 
        -webkit-text-fill-color: transparent; 
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); 
        margin-bottom: 20px; 
        text-align: center;">
        Complete Your WooCommerce Management with Our Must-Have Plugins
    </h1>
        <div class="useful-plugins-container">
            <div class="plugins-list">
                <?php
                // Example data: Replace this with your actual plugin data
                $plugins = [
                    [
                        'name' => 'Client Mate',
                        'image' => 'https://rasmyacademy.shop/wp-content/uploads/2024/09/SYKPlDjvTqC6WqdZNMlPWQ.webp',
                        'description' => 'ClientMate is your all-in-one WooCommerce analytics tool, offering powerful insights into your customers’ orders',
                        'short_description' => 'ClientMate is your all-in-one WooCommerce analytics tool, offering powerful insights into your customers’ orders',
                        'link' => 'https://bit.ly/Clientmate' // Example link, replace with actual link
                    ],
                    [
                        'name' => 'Track Mate',
                        'image' => 'https://rasmyacademy.shop/wp-content/uploads/2024/09/XJJ6aa2OREq6wjPn4nm2jQ.jpg', // Example image URL, replace with actual path
                        'description' => 'perfect solution for WooCommerce store owners who want to give their customers an easy way to track their orders.',
                        'short_description' => 'WooCommerce Order Tracker.',
                        'link' => 'https://bit.ly/TrackwMate' // Example link, replace with actual link
                    ],
					[
                        'name' => 'DeliveryMate',
                        'image' => 'https://rasmyacademy.shop/wp-content/uploads/2024/09/m-OKJY79QbaJXdj4CFHvcw-300x300.jpg', // Example image URL, replace with actual path
                        'description' => 'Transform your WooCommerce store’s delivery process with DeliveryMate, the ultimate plugin for managing delivery companies efficiently and effectively.',
                        'short_description' => 'Streamline Your Delivery Management.',
                        'link' => 'https://bit.ly/DeliveryMate' // Example link, replace with actual link
                    ],

                    // Add more plugins as needed
                ];

                foreach ($plugins as $plugin) : ?>
                    <div class="plugin-item">
                        <a href="<?php echo esc_url($plugin['link']); ?>" target="_blank">
                            <img src="<?php echo esc_url($plugin['image']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>">
                            <h2><?php echo esc_html($plugin['name']); ?></h2>
                        </a>
                        <p class="short-description"><?php echo esc_html($plugin['short_description']); ?></p>
                        <div class="description">
                            <p><?php echo esc_html($plugin['description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <style>
        .useful-plugins-container {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .plugins-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-around;
        }

        .plugin-item {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: calc(33.333% - 20px);
            box-sizing: border-box;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .plugin-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .plugin-item img {
            max-width: 100px;
            height: auto;
            margin-bottom: 10px;
            border-radius: 50%;
        }

        .plugin-item h2 {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }

        .plugin-item .short-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .plugin-item .description {
            display: none;
            font-size: 13px;
            color: #555;
            background: #f4f4f4;
            padding: 10px;
            border-top: 1px solid #ddd;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .plugin-item:hover .description {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .plugin-item {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .plugin-item {
                width: 100%;
            }
        }

        /* Style for the submenu item */
        #toplevel_page_wamate_settings .wp-submenu li a[href="admin.php?page=useful-plugins"] {
            background-color: #28a745 !important; /* Green background */
            color: #ffffff !important; /* White text */
        }

        #toplevel_page_wamate_settings .wp-submenu li a[href="admin.php?page=useful-plugins"]:hover {
            background-color: #218838 !important; /* Darker green on hover */
            color: #ffffff !important; /* Keep text white on hover */
        }
    </style>
    <?php
}