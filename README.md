# Nature Reserves Map WordPress Plugin

A WordPress plugin for managing and displaying nature reserves on an interactive map.

![Screenshot](https://raw.githubusercontent.com/macsplit/nature-reserves-map/refs/heads/main/screenshot.png)

## Features

- **Admin Management**: Add, edit, and delete nature reserves from WordPress admin
- **Interactive Map Picker**: Click on a map to set coordinates for each reserve
- **Shortcode Support**: Display the map anywhere using `[nature_reserves_map]`
- **REST API**: Fetch reserve data via WordPress REST API
- **Responsive Design**: Works on all devices
- **Custom Markers**: Different colors for open/closed reserves

## Installation

1. Upload the `nature-reserves-map` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Nature Reserves' in the admin menu to manage reserves

## Usage

### Display Map

Add the shortcode to any page or post:

```
[nature_reserves_map]
```

### Shortcode Attributes

- `height` - Set custom height (default: "400px")
- `zoom` - Initial zoom level (default: "13")
- `center_lat` - Center latitude (default: "51.3656")
- `center_lng` - Center longitude (default: "-0.1963")
- `show_title` - Show/hide title (default: "true")

Example:
```
[nature_reserves_map height="500px" zoom="12" show_title="false"]
```

### Admin Interface

1. Navigate to **Nature Reserves** in the WordPress admin menu
2. Click **Add New** to create a new reserve
3. Fill in the details:
   - Title
   - Description
   - Click on the map to set location
   - Check "Closed to the public" if applicable
4. Click **Add Reserve** to save

### REST API

Access reserve data at:
```
/wp-json/nature-reserves/v1/reserves
```

## Database

The plugin creates a table `wp_nature_reserves` with the following structure:
- `id` - Primary key
- `title` - Reserve name
- `description` - Reserve description
- `latitude` - Latitude coordinate
- `longitude` - Longitude coordinate
- `is_closed` - Whether closed to public (0/1)
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## License

GPL v2 or later
