<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BEACONET-mini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .navbar h2 { font-size: 24px; }
        .nav-links { display: flex; gap: 20px; }
        .nav-links a, .nav-links button { color: white; text-decoration: none; cursor: pointer; border: none; background: none; font-size: 16px; }
        .nav-links a:hover { text-decoration: underline; }
        .nav-links button:hover { text-decoration: underline; }
        .container { display: flex; height: calc(100vh - 60px); }
        .sidebar { width: 300px; background: white; padding: 20px; border-right: 1px solid #ddd; overflow-y: auto; }
        .main { flex: 1; display: flex; flex-direction: column; }
        #map { flex: 1; }
        .sidebar-section { margin-bottom: 30px; }
        .sidebar-section h3 { color: #667eea; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        textarea { resize: vertical; }
        .btn { width: 100%; padding: 10px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .btn:hover { background: #5568d3; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h2 { color: #333; }
        .close-btn { background: none; border: none; font-size: 28px; cursor: pointer; color: #999; }
        .modal-body { margin-bottom: 20px; }
        .modal-body img { max-width: 100%; border-radius: 5px; margin-bottom: 15px; }
        .modal-footer { display: flex; gap: 10px; }
        .btn-secondary { background: #f0f0f0; color: #333; }
        .btn-secondary:hover { background: #e0e0e0; }
        .item-list { margin-top: 20px; }
        .item { padding: 10px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 10px; cursor: pointer; }
        .item:hover { background: #f9f9f9; }
        .theme-toggle { margin-left: 20px; }
        .search-container { padding: 10px 15px; background: white; border-bottom: 1px solid #ddd; display: flex; gap: 10px; }
        #searchInput { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        #searchBtn { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        #searchBtn:hover { background: #5568d3; }
        .search-results { position: absolute; top: 60px; left: 15px; background: white; border: 1px solid #ddd; border-radius: 5px; max-width: 300px; max-height: 200px; overflow-y: auto; z-index: 500; display: none; }
        .search-result-item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
        .search-result-item:hover { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>BEACONET-mini</h2>
        <div class="nav-links">
            <span>Welcome, {{ auth()->user()->name }}</span>
            <a href="{{ route('notifications.index') }}">Notifications</a>
            <a href="{{ route('settings.index') }}">Settings</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" style="background: #ff9800; padding: 5px 15px; border-radius: 5px;">Admin Panel</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="sidebar">
            <div class="sidebar-section">
                <h3>Post Lost Item</h3>
                <div class="form-group">
                    <label for="title">Item Title</label>
                    <input type="text" id="title" placeholder="What did you lose?">
                </div>
                <div class="form-group">
                    <label for="description">Description (Max 255 chars)</label>
                    <textarea id="description" placeholder="Describe your item..." maxlength="255"></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="location">Location (right-click on map)</label>
                    <input type="text" id="location" placeholder="Click on map to set location" readonly>
                </div>
                <button class="btn" onclick="postLostItem()">Post Item</button>
            </div>

            <div class="sidebar-section">
                <h3>Your Lost Items</h3>
                <div id="yourItems" class="item-list"></div>
            </div>
        </div>

        <div class="main">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search for a location (e.g., New York, Times Square)...">
                <button id="searchBtn" onclick="searchLocation()">Search</button>
                <div class="search-results" id="searchResults"></div>
            </div>
            <div id="map" style="position: relative;"></div>
        </div>
    </div>

    <!-- Item Details Modal -->
    <div class="modal" id="itemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="itemTitle"></h2>
                <button class="close-btn" onclick="closeModal('itemModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>Posted by:</strong> <span id="itemUser"></span></p>
                <p><strong>Description:</strong> <span id="itemDesc"></span></p>
                <img id="itemImage" src="" alt="Item image" style="max-height: 300px;">
                <button class="btn" onclick="openFoundModal()">Found this item?</button>
            </div>
        </div>
    </div>

    <!-- Found Report Modal -->
    <div class="modal" id="foundModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Report Found Item</h2>
                <button class="close-btn" onclick="closeModal('foundModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="foundMessage">Message</label>
                    <textarea id="foundMessage" placeholder="Let them know how you found it..." maxlength="500"></textarea>
                </div>
                <div class="form-group">
                    <label for="foundImage">Photo of the item</label>
                    <input type="file" id="foundImage" accept="image/*">
                </div>
                <button class="btn" onclick="submitFoundReport()">Submit Report</button>
                <button class="btn btn-secondary" onclick="closeModal('foundModal')">Cancel</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        let map;
        let selectedLat = null;
        let selectedLng = null;
        let selectedItemId = null;
        let searchTimeout;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            map = L.map('map').setView([40, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Load lost items
            loadLostItems();

            // Right-click to set location
            map.on('contextmenu', function(e) {
                selectedLat = e.latlng.lat;
                selectedLng = e.latlng.lng;
                document.getElementById('location').value = `${selectedLat.toFixed(4)}, ${selectedLng.toFixed(4)}`;
                
                // Remove old marker if exists
                if (window.currentMarker) map.removeLayer(window.currentMarker);
                window.currentMarker = L.circleMarker([selectedLat, selectedLng], {
                    color: '#667eea',
                    radius: 8,
                    fillOpacity: 0.7
                }).addTo(map);
            });

            // Search input listener
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchLocation();
                }
            });

            // Real-time search suggestions
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                if (e.target.value.length > 2) {
                    searchTimeout = setTimeout(() => fetchSearchSuggestions(e.target.value), 300);
                } else {
                    document.getElementById('searchResults').style.display = 'none';
                }
            });
        });

        function fetchSearchSuggestions(query) {
            const resultsDiv = document.getElementById('searchResults');
            resultsDiv.innerHTML = '';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
                .then(r => r.json())
                .then(results => {
                    if (results.length === 0) {
                        resultsDiv.innerHTML = '<div class="search-result-item">No results found</div>';
                    } else {
                        resultsDiv.innerHTML = results.map(result => 
                            `<div class="search-result-item" onclick="selectSearchResult(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}')">${result.display_name}</div>`
                        ).join('');
                    }
                    resultsDiv.style.display = 'block';
                })
                .catch(e => console.error('Search error:', e));
        }

        function searchLocation() {
            const query = document.getElementById('searchInput').value.trim();
            if (!query) {
                alert('Please enter a location to search');
                return;
            }

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                .then(r => r.json())
                .then(results => {
                    if (results.length === 0) {
                        alert('Location not found. Please try a different search.');
                        return;
                    }

                    const result = results[0];
                    const lat = parseFloat(result.lat);
                    const lon = parseFloat(result.lon);

                    // Pan and zoom to location
                    map.setView([lat, lon], 15);

                    // Add a temporary marker at the search location
                    if (window.searchMarker) map.removeLayer(window.searchMarker);
                    window.searchMarker = L.circleMarker([lat, lon], {
                        color: '#ff6b6b',
                        radius: 10,
                        fillOpacity: 0.8,
                        weight: 2
                    }).addTo(map);

                    window.searchMarker.bindPopup(`<strong>Search Result</strong><br>${result.display_name}`).openPopup();

                    // Hide search results
                    document.getElementById('searchResults').style.display = 'none';
                })
                .catch(e => {
                    console.error('Error:', e);
                    alert('Error searching for location. Please try again.');
                });
        }

        function selectSearchResult(lat, lon, name) {
            // Pan and zoom to location
            map.setView([lat, lon], 15);

            // Add a temporary marker at the selected location
            if (window.searchMarker) map.removeLayer(window.searchMarker);
            window.searchMarker = L.circleMarker([lat, lon], {
                color: '#ff6b6b',
                radius: 10,
                fillOpacity: 0.8,
                weight: 2
            }).addTo(map);

            window.searchMarker.bindPopup(`<strong>${name}</strong>`).openPopup();

            // Update search input
            document.getElementById('searchInput').value = name;

            // Hide search results
            document.getElementById('searchResults').style.display = 'none';
        }

        function loadLostItems() {
            // Load all items for map
            fetch('{{ route("lost-items.index") }}')
                .then(r => r.json())
                .then(items => {
                    items.forEach(item => {
                        // Add marker to map
                        const marker = L.circleMarker([item.latitude, item.longitude], {
                            color: '#667eea',
                            radius: 8,
                            fillOpacity: 0.7
                        }).addTo(map);

                        marker.on('click', function() {
                            openItemModal(item);
                        });

                        marker.bindPopup(item.title);
                    });
                })
                .catch(e => console.error('Error loading items:', e));

            // Load only user's items for sidebar
            fetch('{{ route("lost-items.myItems") }}')
                .then(r => r.json())
                .then(items => {
                    const container = document.getElementById('yourItems');
                    if (items.length === 0) {
                        container.innerHTML = '<p style="color: #999; font-size: 14px;">No items posted yet</p>';
                    } else {
                        container.innerHTML = items.map(item => 
                            `<div class="item">${item.title} - <span style="color: ${item.status === 'lost' ? '#ff6b6b' : '#51cf66'}">${item.status}</span></div>`
                        ).join('');
                    }
                })
                .catch(e => console.error('Error loading your items:', e));
        }

        function postLostItem() {
            if (!selectedLat || !selectedLng) {
                alert('Please right-click on the map to select a location');
                return;
            }

            const title = document.getElementById('title').value.trim();
            if (!title) {
                alert('Please enter an item title');
                return;
            }

            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', document.getElementById('description').value);
            formData.append('latitude', selectedLat);
            formData.append('longitude', selectedLng);
            formData.append('location_name', document.getElementById('location').value);
            
            const imageFile = document.getElementById('image').files[0];
            if (imageFile) formData.append('image', imageFile);

            fetch('{{ route("lost-items.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => {
                if (!r.ok) {
                    return r.text().then(text => {
                        throw new Error(`Server error (${r.status}): ${text}`);
                    });
                }
                return r.json();
            })
            .then(data => {
                alert('Item posted successfully!');
                document.getElementById('title').value = '';
                document.getElementById('description').value = '';
                document.getElementById('image').value = '';
                document.getElementById('location').value = '';
                selectedLat = null;
                selectedLng = null;
                if (window.currentMarker) map.removeLayer(window.currentMarker);
                location.reload();
            })
            .catch(e => {
                console.error('Error details:', e);
                alert('Error posting item: ' + e.message);
            });
        }

        function openItemModal(item) {
            selectedItemId = item.id;
            document.getElementById('itemTitle').textContent = item.title;
            document.getElementById('itemUser').textContent = item.user.name;
            document.getElementById('itemDesc').textContent = item.description || 'No description';
            if (item.image_path) {
                document.getElementById('itemImage').src = '/storage/' + item.image_path;
            }
            document.getElementById('itemModal').classList.add('active');
        }

        function openFoundModal() {
            closeModal('itemModal');
            document.getElementById('foundModal').classList.add('active');
        }

        function submitFoundReport() {
            const formData = new FormData();
            formData.append('lost_item_id', selectedItemId);
            formData.append('message', document.getElementById('foundMessage').value);
            
            const imageFile = document.getElementById('foundImage').files[0];
            if (imageFile) formData.append('image', imageFile);

            fetch('{{ route("found-reports.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => {
                if (!r.ok) {
                    return r.text().then(text => {
                        throw new Error(`Server error (${r.status}): ${text}`);
                    });
                }
                return r.json();
            })
            .then(data => {
                alert('Report submitted successfully!');
                document.getElementById('foundMessage').value = '';
                document.getElementById('foundImage').value = '';
                closeModal('foundModal');
                location.reload();
            })
            .catch(e => {
                console.error('Error details:', e);
                alert('Error submitting report: ' + e.message);
            });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        window.onclick = function(event) {
            const modal = event.target;
            if (modal.classList.contains('modal')) {
                modal.classList.remove('active');
            }
        }
    </script>
</body>
</html>
