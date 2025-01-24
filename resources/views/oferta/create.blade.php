<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Oferta</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDX8c0ulrUE5aUsefFR-EXM1NQlIAa8QyU&callback=initMap&libraries=&v=weekly" async></script>

    <script>
        // Función para inicializar el mapa
        let map;
        let marker;

        function initMap() {
            const mapOptions = {
                center: { lat: -17.7833, lng: -63.1825 }, // Coordenadas de Santa Cruz, Bolivia
                zoom: 12,
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Agregar un marcador en el mapa
            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: map.getCenter()
            });

            // Escuchar cuando el marcador se mueve
            google.maps.event.addListener(marker, 'dragend', function() {
                const position = marker.getPosition();
                document.getElementById('latitud').value = position.lat();
                document.getElementById('longitud').value = position.lng();
            });
        }
    </script>
</head>
<body>
    <h1>Crear Oferta de Reciclaje</h1>

    <form action="{{ route('oferta.store') }}" method="POST">
        @csrf
        <label for="latitud">Latitud:</label>
        <input type="text" id="latitud" name="latitud" readonly><br><br>

        <label for="longitud">Longitud:</label>
        <input type="text" id="longitud" name="longitud" readonly><br><br>

        <label for="cantidad">Cantidad de Latas:</label>
        <input type="number" id="cantidad" name="cantidad" required><br><br>

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" required><br><br>

        <button type="submit">Crear Oferta</button>
    </form>

    <!-- Contenedor para el mapa -->
    <div id="map" style="height: 400px; width: 100%;"></div>

</body>
</html>
