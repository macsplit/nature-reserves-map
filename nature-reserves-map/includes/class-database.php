<?php
/**
 * Database handler for Nature Reserves Map
 */

if (!defined('ABSPATH')) {
    exit;
}

class NRM_Database {
    
    /**
     * Create database table
     */
    public static function create_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'nature_reserves';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            latitude decimal(10, 8) NOT NULL,
            longitude decimal(11, 8) NOT NULL,
            is_closed tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY title_idx (title)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Add option to track database version
        add_option('nrm_db_version', '1.0.0');
        
        // Insert default data if table is empty
        self::insert_default_data();
    }
    
    /**
     * Insert default data
     */
    private static function insert_default_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nature_reserves';
        
        // Check if table is empty
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($count == 0) {
            $default_reserves = [
                [
                    'title' => 'Belmont Pastures',
                    'description' => "A diverse nature reserve with chalk grassland habitats. Part of the London Borough of Sutton's network of over 30 managed sites.",
                    'latitude' => 51.346215,
                    'longitude' => -0.198269,
                    'is_closed' => 0
                ],
                [
                    'title' => "Queen Mary's Woodland",
                    'description' => 'A 6-hectare area of semi-natural secondary woodland and Site of Borough Importance for Nature Conservation (Grade 1).',
                    'latitude' => 51.348949,
                    'longitude' => -0.168507,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Sutton Ecology Centre Grounds',
                    'description' => 'The grounds of Sutton Ecology Centre, featuring forest and butterfly gardens, marshes, meadows and more.',
                    'latitude' => 51.366248,
                    'longitude' => -0.165117,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Therapia Lane Rough',
                    'description' => 'Former rail sidings that was once one of the most botanically diverse sites in Greater London with over 230 vascular plant species.',
                    'latitude' => 51.387035,
                    'longitude' => -0.131600,
                    'is_closed' => 1
                ],
                [
                    'title' => 'Wandle Valley Wetland',
                    'description' => 'A 0.6-hectare Local Nature Reserve with open water, seasonal pools, scrub and wet woodland.',
                    'latitude' => 51.384725,
                    'longitude' => -0.164151,
                    'is_closed' => 1
                ],
                [
                    'title' => 'Roundshaw Downs',
                    'description' => 'At 38 hectares, the largest area of unimproved chalk grassland in the borough. Former site of Croydon Airport.',
                    'latitude' => 51.350236,
                    'longitude' => -0.123253,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Anton Crescent Wetlands',
                    'description' => 'A 1-hectare Local Nature Reserve opened in 2007. Features pond, reedbed, neutral grassland and wetland habitats.',
                    'latitude' => 51.372611,
                    'longitude' => -0.202045,
                    'is_closed' => 1
                ],
                [
                    'title' => 'Sutton Common',
                    'description' => 'Features newly created lowland wet grassland habitat in the northern area.',
                    'latitude' => 51.386392,
                    'longitude' => -0.203612,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Carew Manor Wetlands',
                    'description' => 'Wetland habitat associated with the historic Carew Manor area in Carshalton.',
                    'latitude' => 51.371821,
                    'longitude' => -0.135956,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Carshalton Road Pastures',
                    'description' => 'Chalk grassland site featuring typical downland species.',
                    'latitude' => 51.333038,
                    'longitude' => -0.166512,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Cuddington Meadows',
                    'description' => 'A 1.42-hectare Local Nature Reserve declared in 2004. Features kidney vetch, the food plant of the rare Small Blue butterfly.',
                    'latitude' => 51.334687,
                    'longitude' => -0.213504,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Avenue Primary School Nature Area',
                    'description' => 'Wildlife garden at Avenue Primary School, one of three sites in the borough supporting the Small Blue butterfly.',
                    'latitude' => 51.347046,
                    'longitude' => -0.205028,
                    'is_closed' => 1
                ],
                [
                    'title' => 'Devonshire Avenue Nature Area',
                    'description' => 'A small 0.42-hectare Local Nature Reserve in South Sutton declared in 2004.',
                    'latitude' => 51.354316,
                    'longitude' => -0.187851,
                    'is_closed' => 0
                ],
                [
                    'title' => 'The Warren',
                    'description' => "Nature reserve site managed as part of Sutton's network of over 30 conservation areas.",
                    'latitude' => 51.360795,
                    'longitude' => -0.180545,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Wellfield North and Grasslands Complex',
                    'description' => 'Chalk grassland habitat complex featuring flower-rich meadows.',
                    'latitude' => 51.348574,
                    'longitude' => -0.170846,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Kimpton Balancing Pond and Buffer Strip',
                    'description' => 'Flood management facility that also provides valuable wildlife habitat.',
                    'latitude' => 51.378986,
                    'longitude' => -0.214856,
                    'is_closed' => 0
                ],
                [
                    'title' => 'The Spinney',
                    'description' => 'Woodland nature reserve providing important tree and shrub habitat within the urban environment.',
                    'latitude' => 51.375330,
                    'longitude' => -0.162327,
                    'is_closed' => 0
                ],
                [
                    'title' => 'Ruffett and Bigwood',
                    'description' => 'Woodland site managed by Sutton Nature Conservation Volunteers.',
                    'latitude' => 51.328708,
                    'longitude' => -0.162091,
                    'is_closed' => 0
                ]
            ];
            
            foreach ($default_reserves as $reserve) {
                $wpdb->insert($table_name, $reserve);
            }
        }
    }
    
    /**
     * Get all reserves
     */
    public static function get_all_reserves($order_by = 'title', $order = 'ASC') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nature_reserves';
        
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY %s %s",
            $order_by,
            $order
        );
        
        // WordPress prepare doesn't handle ORDER BY well, so we'll do it safely
        $allowed_order_by = ['title', 'created_at', 'updated_at', 'id'];
        $allowed_order = ['ASC', 'DESC'];
        
        if (!in_array($order_by, $allowed_order_by)) {
            $order_by = 'title';
        }
        if (!in_array($order, $allowed_order)) {
            $order = 'ASC';
        }
        
        $sql = "SELECT * FROM $table_name ORDER BY $order_by $order";
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Get single reserve
     */
    public static function get_reserve($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nature_reserves';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Insert reserve
     */
    public static function insert_reserve($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nature_reserves';
        
        return $wpdb->insert($table_name, $data);
    }
    
    /**
     * Update reserve
     */
    public static function update_reserve($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nature_reserves';
        
        return $wpdb->update(
            $table_name,
            $data,
            ['id' => $id]
        );
    }
    
    /**
     * Delete reserve
     */
    public static function delete_reserve($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nature_reserves';
        
        return $wpdb->delete(
            $table_name,
            ['id' => $id]
        );
    }
    
    /**
     * Deactivation cleanup
     */
    public static function deactivate() {
        // Optionally delete table on deactivation
        // For now, we'll keep the data
    }
}