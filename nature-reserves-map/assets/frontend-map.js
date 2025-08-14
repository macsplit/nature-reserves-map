/**
 * Frontend map for Nature Reserves
 */

document.addEventListener('DOMContentLoaded', function() {
    // Wait for MapLibre to load
    if (typeof maplibregl === 'undefined') {
        setTimeout(initNatureReservesMap, 100);
        return;
    }
    
    initNatureReservesMap();
});

function initNatureReservesMap() {
    // Find all map instances on the page
    const mapContainers = document.querySelectorAll('.nrm-map-instance');
    
    mapContainers.forEach(function(container) {
        if (container.hasChildNodes()) {
            return; // Already initialized
        }
        
        // Get map settings from data attributes
        const zoom = parseFloat(container.dataset.zoom) || 13;
        const centerLat = parseFloat(container.dataset.centerLat) || 51.3656;
        const centerLng = parseFloat(container.dataset.centerLng) || -0.1963;
        
        // Initialize map
        const map = new maplibregl.Map({
            container: container.id,
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
            center: [centerLng, centerLat],
            zoom: zoom
        });
        
        // Add navigation controls
        map.addControl(new maplibregl.NavigationControl());
        
        // Load reserves data from API
        map.on('load', function() {
            fetch(nrm_data.api_url)
                .then(response => response.json())
                .then(reserves => {
                    reserves.forEach(pin => {
                        // Generate slug for link
                        const slug = pin.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                        const linkedTitle = `<a href="https://sncv.org.uk/wildlife-on-suttons-doorstep/sites/${slug}/" target="_blank" rel="noopener">${pin.title}</a>`;
                        
                        // Create popup content with custom classes
                        let popupContent = `<div class="nrm-popup-wrapper">
                            <div class="nrm-popup-title">${linkedTitle}</div>
                            <div class="nrm-popup-description">${pin.description}</div>`;
                        if (pin.closed) {
                            popupContent += '<div class="nrm-popup-warning">⚠️ Closed to the public</div>';
                        }
                        popupContent += '</div>';
                        
                        // Create popup
                        const popup = new maplibregl.Popup({ offset: 25 })
                            .setHTML(popupContent);
                        
                        // Create marker
                        new maplibregl.Marker({
                            color: pin.closed ? '#ef4444' : '#22c55e'
                        })
                        .setLngLat([pin.lng, pin.lat])
                        .setPopup(popup)
                        .addTo(map);
                    });
                })
                .catch(error => {
                    console.error('Error loading reserves:', error);
                });
        });
    });
}