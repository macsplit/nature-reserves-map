<?php
/**
 * Admin handler for Nature Reserves Map
 */

if (!defined('ABSPATH')) {
    exit;
}

class NRM_Admin {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_post_nrm_save_reserve', [$this, 'save_reserve']);
        add_action('admin_post_nrm_delete_reserve', [$this, 'delete_reserve']);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Nature Reserves',
            'Nature Reserves',
            'manage_options',
            'nature-reserves',
            [$this, 'render_list_page'],
            'dashicons-location-alt',
            30
        );
        
        add_submenu_page(
            'nature-reserves',
            'All Reserves',
            'All Reserves',
            'manage_options',
            'nature-reserves',
            [$this, 'render_list_page']
        );
        
        add_submenu_page(
            'nature-reserves',
            'Add New Reserve',
            'Add New',
            'manage_options',
            'nature-reserves-add',
            [$this, 'render_edit_page']
        );
        
        // Hidden page for editing
        add_submenu_page(
            null,
            'Edit Reserve',
            'Edit Reserve',
            'manage_options',
            'nature-reserves-edit',
            [$this, 'render_edit_page']
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (!in_array($hook, ['toplevel_page_nature-reserves', 'nature-reserves_page_nature-reserves-add', 'admin_page_nature-reserves-edit'])) {
            return;
        }
        
        // Enqueue MapLibre for admin
        if ($hook === 'nature-reserves_page_nature-reserves-add' || $hook === 'admin_page_nature-reserves-edit') {
            wp_enqueue_style('maplibre-css', 'https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css');
            wp_enqueue_script('maplibre-js', 'https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js', [], null, true);
            wp_enqueue_script('nrm-admin-map', NRM_PLUGIN_URL . 'assets/admin-map.js', ['maplibre-js'], NRM_PLUGIN_VERSION, true);
            wp_enqueue_style('nrm-admin', NRM_PLUGIN_URL . 'assets/admin.css', [], NRM_PLUGIN_VERSION);
        }
    }
    
    /**
     * Render list page
     */
    public function render_list_page() {
        $reserves = NRM_Database::get_all_reserves('title', 'ASC');
        
        // Handle messages
        $message = '';
        if (isset($_GET['message'])) {
            switch ($_GET['message']) {
                case 'saved':
                    $message = '<div class="notice notice-success is-dismissible"><p>Reserve saved successfully.</p></div>';
                    break;
                case 'deleted':
                    $message = '<div class="notice notice-success is-dismissible"><p>Reserve deleted successfully.</p></div>';
                    break;
                case 'error':
                    $message = '<div class="notice notice-error is-dismissible"><p>An error occurred. Please try again.</p></div>';
                    break;
            }
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Nature Reserves</h1>
            <a href="<?php echo admin_url('admin.php?page=nature-reserves-add'); ?>" class="page-title-action">Add New</a>
            
            <?php echo $message; ?>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Coordinates</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reserves): ?>
                        <?php foreach ($reserves as $reserve): ?>
                            <tr>
                                <td><strong><?php echo esc_html($reserve->title); ?></strong></td>
                                <td><?php echo wp_trim_words(esc_html($reserve->description), 20); ?></td>
                                <td><?php echo esc_html($reserve->latitude . ', ' . $reserve->longitude); ?></td>
                                <td>
                                    <?php if ($reserve->is_closed): ?>
                                        <span style="color: #d63638;">Closed to public</span>
                                    <?php else: ?>
                                        <span style="color: #00a32a;">Open</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=nature-reserves-edit&id=' . $reserve->id); ?>" class="button button-small">Edit</a>
                                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;">
                                        <?php wp_nonce_field('nrm_delete_reserve'); ?>
                                        <input type="hidden" name="action" value="nrm_delete_reserve">
                                        <input type="hidden" name="reserve_id" value="<?php echo $reserve->id; ?>">
                                        <button type="submit" class="button button-small" onclick="return confirm('Are you sure you want to delete this reserve?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No reserves found. <a href="<?php echo admin_url('admin.php?page=nature-reserves-add'); ?>">Add your first reserve</a>.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px;">
                <h3>Shortcode Usage</h3>
                <p>To display the map on any page or post, use the shortcode: <code>[nature_reserves_map]</code></p>
                <p>You can also customize the map with attributes:</p>
                <ul>
                    <li><code>[nature_reserves_map height="500px"]</code> - Set custom height</li>
                    <li><code>[nature_reserves_map zoom="13"]</code> - Set initial zoom level</li>
                    <li><code>[nature_reserves_map center_lat="51.3656" center_lng="-0.1963"]</code> - Set center coordinates</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render edit page
     */
    public function render_edit_page() {
        $reserve = null;
        $is_edit = false;
        
        if (isset($_GET['id'])) {
            $reserve = NRM_Database::get_reserve($_GET['id']);
            $is_edit = true;
        }
        ?>
        <div class="wrap">
            <h1><?php echo $is_edit ? 'Edit Reserve' : 'Add New Reserve'; ?></h1>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('nrm_save_reserve'); ?>
                <input type="hidden" name="action" value="nrm_save_reserve">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="reserve_id" value="<?php echo $reserve->id; ?>">
                <?php endif; ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="title">Title</label></th>
                        <td>
                            <input type="text" name="title" id="title" class="regular-text" 
                                   value="<?php echo $is_edit ? esc_attr($reserve->title) : ''; ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="description">Description</label></th>
                        <td>
                            <textarea name="description" id="description" rows="5" class="large-text"><?php echo $is_edit ? esc_textarea($reserve->description) : ''; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="is_closed">Status</label></th>
                        <td>
                            <label>
                                <input type="checkbox" name="is_closed" id="is_closed" value="1" 
                                       <?php echo ($is_edit && $reserve->is_closed) ? 'checked' : ''; ?>>
                                Closed to the public
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Location</label></th>
                        <td>
                            <div style="margin-bottom: 10px;">
                                <label>Latitude: <input type="text" name="latitude" id="latitude" 
                                       value="<?php echo $is_edit ? esc_attr($reserve->latitude) : '51.3656'; ?>" 
                                       required readonly style="width: 150px;"></label>
                                <label style="margin-left: 20px;">Longitude: <input type="text" name="longitude" id="longitude" 
                                       value="<?php echo $is_edit ? esc_attr($reserve->longitude) : '-0.1963'; ?>" 
                                       required readonly style="width: 150px;"></label>
                            </div>
                            <p class="description">Click on the map below to set the location</p>
                            <div id="admin-map" style="width: 100%; max-width: 800px; height: 400px; border: 1px solid #ccc; margin-top: 10px;"></div>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" class="button button-primary" value="<?php echo $is_edit ? 'Update Reserve' : 'Add Reserve'; ?>">
                    <a href="<?php echo admin_url('admin.php?page=nature-reserves'); ?>" class="button">Cancel</a>
                </p>
            </form>
        </div>
        
        <script>
            // Initialize coordinates for the map
            window.nrmInitialCoords = {
                lat: <?php echo $is_edit ? $reserve->latitude : '51.3656'; ?>,
                lng: <?php echo $is_edit ? $reserve->longitude : '-0.1963'; ?>
            };
        </script>
        <?php
    }
    
    /**
     * Save reserve
     */
    public function save_reserve() {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'nrm_save_reserve')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $data = [
            'title' => sanitize_text_field(wp_unslash($_POST['title'])),
            'description' => sanitize_textarea_field(wp_unslash($_POST['description'])),
            'latitude' => floatval($_POST['latitude']),
            'longitude' => floatval($_POST['longitude']),
            'is_closed' => isset($_POST['is_closed']) ? 1 : 0
        ];
        
        if (isset($_POST['reserve_id'])) {
            // Update existing
            $result = NRM_Database::update_reserve($_POST['reserve_id'], $data);
        } else {
            // Insert new
            $result = NRM_Database::insert_reserve($data);
        }
        
        if ($result !== false) {
            wp_redirect(admin_url('admin.php?page=nature-reserves&message=saved'));
        } else {
            wp_redirect(admin_url('admin.php?page=nature-reserves&message=error'));
        }
        exit;
    }
    
    /**
     * Delete reserve
     */
    public function delete_reserve() {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'nrm_delete_reserve')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $result = NRM_Database::delete_reserve($_POST['reserve_id']);
        
        if ($result !== false) {
            wp_redirect(admin_url('admin.php?page=nature-reserves&message=deleted'));
        } else {
            wp_redirect(admin_url('admin.php?page=nature-reserves&message=error'));
        }
        exit;
    }
}