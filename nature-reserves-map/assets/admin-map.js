/**
 * Admin map for selecting coordinates
 */

document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('admin-map')) {
        return;
    }
    
    // Wait for MapLibre to load
    if (typeof maplibregl === 'undefined') {
        setTimeout(initAdminMap, 100);
        return;
    }
    
    initAdminMap();
});

function initAdminMap() {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    // Get initial coordinates
    const initialLat = window.nrmInitialCoords ? window.nrmInitialCoords.lat : 51.3656;
    const initialLng = window.nrmInitialCoords ? window.nrmInitialCoords.lng : -0.1963;
    
    // Initialize map
    const map = new maplibregl.Map({
        container: 'admin-map',
        style: {
            version: 8,
            sources: {
                'osm': {
                    type: 'raster',
                    tiles: ['https://tile.openstreetmap.org/{z}/{x}/{y}.png'],
                    tileSize: 256,
                    attribution: '© OpenStreetMap contributors'
                }
            },
            layers: [{
                id: 'osm',
                type: 'raster',
                source: 'osm',
                minzoom: 0,
                maxzoom: 19
            }]
        },
        center: [initialLng, initialLat],
        zoom: 13
    });
    
    // Add navigation controls
    map.addControl(new maplibregl.NavigationControl());
    
    // Create marker
    let marker = new maplibregl.Marker({
        draggable: true,
        color: '#22c55e'
    })
    .setLngLat([initialLng, initialLat])
    .addTo(map);
    
    // Update coordinates when marker is dragged
    marker.on('dragend', function() {
        const lngLat = marker.getLngLat();
        updateCoordinates(lngLat.lat, lngLat.lng);
    });
    
    // Update marker and coordinates when map is clicked
    map.on('click', function(e) {
        marker.setLngLat(e.lngLat);
        updateCoordinates(e.lngLat.lat, e.lngLat.lng);
    });
    
    // Function to update coordinate inputs
    function updateCoordinates(lat, lng) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        
        // Remove readonly temporarily to allow programmatic update
        latInput.removeAttribute('readonly');
        lngInput.removeAttribute('readonly');
        
        // Trigger change event
        latInput.dispatchEvent(new Event('change'));
        lngInput.dispatchEvent(new Event('change'));
        
        // Re-add readonly
        setTimeout(() => {
            latInput.setAttribute('readonly', 'readonly');
            lngInput.setAttribute('readonly', 'readonly');
        }, 10);
    }
}