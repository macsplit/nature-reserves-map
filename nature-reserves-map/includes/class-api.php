<?php
/**
 * API handler for Nature Reserves Map
 */

if (!defined('ABSPATH')) {
    exit;
}

class NRM_API {
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('nature-reserves/v1', '/reserves', [
            'methods' => 'GET',
            'callback' => [$this, 'get_reserves'],
            'permission_callback' => '__return_true', // Public endpoint
        ]);
    }
    
    /**
     * Get reserves endpoint
     */
    public function get_reserves($request) {
        $reserves = NRM_Database::get_all_reserves('title', 'ASC');
        
        // Format data for frontend
        $formatted_reserves = [];
        foreach ($reserves as $reserve) {
            $formatted_reserves[] = [
                'lat' => floatval($reserve->latitude),
                'lng' => floatval($reserve->longitude),
                'title' => wp_specialchars_decode($reserve->title, ENT_QUOTES),
                'description' => wp_specialchars_decode($reserve->description, ENT_QUOTES),
                'closed' => (bool) $reserve->is_closed
            ];
        }
        
        return new WP_REST_Response($formatted_reserves, 200);
    }
}