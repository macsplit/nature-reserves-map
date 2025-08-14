<?php
/**
 * Shortcode handler for Nature Reserves Map
 */

if (!defined('ABSPATH')) {
    exit;
}

class NRM_Shortcode {
    
    public function __construct() {
        add_shortcode('nature_reserves_map', [$this, 'render_map']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (has_shortcode(get_post()->post_content ?? '', 'nature_reserves_map')) {
            wp_enqueue_style('maplibre-css', 'https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css');
            wp_enqueue_script('maplibre-js', 'https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js', [], null, true);
            wp_enqueue_script('nrm-frontend-map', NRM_PLUGIN_URL . 'assets/frontend-map.js', ['maplibre-js'], NRM_PLUGIN_VERSION, true);
            wp_enqueue_style('nrm-frontend', NRM_PLUGIN_URL . 'assets/frontend.css', [], NRM_PLUGIN_VERSION);
            
            // Pass API URL to JavaScript
            wp_localize_script('nrm-frontend-map', 'nrm_data', [
                'api_url' => rest_url('nature-reserves/v1/reserves'),
                'plugin_url' => NRM_PLUGIN_URL
            ]);
        }
    }
    
    /**
     * Render map shortcode
     */
    public function render_map($atts) {
        $atts = shortcode_atts([
            'height' => '400px',
            'zoom' => '13',
            'center_lat' => '51.3656',
            'center_lng' => '-0.1963',
            'show_title' => 'true'
        ], $atts);
        
        // Generate unique ID for this map instance
        $map_id = 'nrm-map-' . uniqid();
        
        ob_start();
        ?>
        <div class="nrm-map-container">
            <?php if ($atts['show_title'] === 'true'): ?>
                <h2 class="nrm-map-title">Sutton Nature Reserves</h2>
            <?php endif; ?>
            <div id="<?php echo esc_attr($map_id); ?>" 
                 class="nrm-map-instance" 
                 style="height: <?php echo esc_attr($atts['height']); ?>;"
                 data-zoom="<?php echo esc_attr($atts['zoom']); ?>"
                 data-center-lat="<?php echo esc_attr($atts['center_lat']); ?>"
                 data-center-lng="<?php echo esc_attr($atts['center_lng']); ?>"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}