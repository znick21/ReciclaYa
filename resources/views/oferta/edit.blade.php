@extends('layouts.app')

@section('content')
@include('partials.ofertas-styles')
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta de Reciclaje</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDX8c0ulrUE5aUsefFR-EXM1NQlIAa8QyU&callback=initMap&libraries=&v=weekly" async></script>

    <script>
        let map;
        let marker;
        let geocoder;
        let mapClickable = false;

        function initMap() {
            const mapOptions = {
                center: {
                    lat: -17.7833,
                    lng: -63.1825
                },
                zoom: 12,
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            geocoder = new google.maps.Geocoder();

            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: map.getCenter(),
                visible: false,
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                const position = marker.getPosition();
                document.getElementById('latitud').value = position.lat();
                document.getElementById('longitud').value = position.lng();

                geocodeLatLng(position, function(address) {
                    document.getElementById('ubicacion').value = address;
                });
            });

            google.maps.event.addListener(map, 'click', function(event) {
                if (mapClickable) {
                    const position = event.latLng;
                    marker.setPosition(position);
                    marker.setVisible(true);
                    document.getElementById('latitud').value = position.lat();
                    document.getElementById('longitud').value = position.lng();
                    geocodeLatLng(position, function(address) {
                        document.getElementById('ubicacion').value = address;
                    });
                }
            });
        }

        function enableMapSelection() {
            mapClickable = true;
            document.getElementById('select-button').disabled = true;
            document.getElementById('map').style.height = "400px";
        }

        function geocodeLatLng(latLng, callback) {
            geocoder.geocode({
                'location': latLng
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        const address = results[0].formatted_address;
                        callback(address);
                        updateLocationDropdown(address);
                    } else {
                        callback("Dirección no encontrada");
                        updateLocationDropdown("Dirección no encontrada");
                    }
                } else {
                    callback("Geocodificación fallida: " + status);
                    updateLocationDropdown("Error en la geocodificación");
                }
            });
        }

        function updateLocationDropdown(address) {
            const dropdown = document.getElementById('ubicacion');
            dropdown.innerHTML = '';
            const option = document.createElement('option');
            option.value = address;
            option.textContent = address;
            dropdown.appendChild(option);
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Editar Oferta de Reciclaje</h1>

        <div class="form-container">
            <form action="{{ route('oferta.update', $oferta->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')  <!-- Para indicar que es un PUT -->

                <input type="text" id="latitud" name="latitud" value="{{ old('latitud', $oferta->latitud) }}" readonly hidden>
                <input type="text" id="longitud" name="longitud" value="{{ old('longitud', $oferta->longitud) }}" readonly hidden>

                <label for="ubicacion">Ubicación Seleccionada:</label>
                <select id="ubicacion" name="ubicacion" class="address-dropdown" disabled>
                    <option value="{{ old('ubicacion', $oferta->ubicacion) }}">{{ old('ubicacion', $oferta->ubicacion) }}</option>
                </select>

                <label for="material">Material:</label>
                <select id="material" name="material" required>
                    <option value="papel" {{ old('material', $oferta->material) == 'papel' ? 'selected' : '' }}>Papel</option>
                    <option value="carton" {{ old('material', $oferta->material) == 'carton' ? 'selected' : '' }}>Cartón</option>
                    <option value="plastico" {{ old('material', $oferta->material) == 'plastico' ? 'selected' : '' }}>Plástico</option>
                    <option value="vidrio" {{ old('material', $oferta->material) == 'vidrio' ? 'selected' : '' }}>Vidrio</option>
                    <option value="metales" {{ old('material', $oferta->material) == 'metales' ? 'selected' : '' }}>Metales</option>
                    <option value="orgánico" {{ old('material', $oferta->material) == 'orgánico' ? 'selected' : '' }}>Orgánico</option>
                    <option value="electrónico" {{ old('material', $oferta->material) == 'electrónico' ? 'selected' : '' }}>Electrónico</option>
                </select>

                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" required placeholder="Cantidad de producto en Kg" value="{{ old('cantidad', $oferta->cantidad) }}">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" required placeholder="Precio ofertado en Bs" value="{{ old('precio', $oferta->precio) }}">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <!-- Mostrar la imagen actual -->
                @if($oferta->imagen)
                    <img src="{{ asset('storage/' . $oferta->imagen) }}" alt="Imagen actual" style="max-width: 200px; margin-top: 10px;">
                @endif

                <button type="submit">Actualizar Oferta</button>
            </form>
        </div>

        <div class="form-container button-container">
            <button id="select-button" onclick="enableMapSelection()">Seleccionar en el mapa</button>
        </div>

        <div class="form-container button-container">
            <button onclick="window.history.back();" class="btn btn-danger">Cancelar</button>
        </div>

        <div class="map-container">
            <div id="map" style="height: 400px;"></div>
        </div>
    </div>
</body>

</html>
@endsection
