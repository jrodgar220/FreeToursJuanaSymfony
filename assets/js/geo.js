
window.addEventListener("load", function(){
    console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

   
        var labelElement = document.querySelector('label[for="Item_latitud"]');
        if(labelElement){

        
        // Agregar Google Maps API
        var googleMapsScript = document.createElement('script');
        googleMapsScript.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDeXNRT5we4NFDzjajuV1_T1cv_Wgbc-ic'; // Reemplaza con tu clave API de Google Maps
        document.head.appendChild(googleMapsScript);
       

        var jqueryScript = document.createElement('script');
        jqueryScript.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js';
        jqueryScript.onload = function() {
            // Tu código que utiliza jQuery aquí
            $(document).ready(function() {
                // Inicializar el mapa en el modal
                mapaModal = new google.maps.Map(document.getElementById("mapa-modal"), {
                    zoom: 16,
                    scrollwheel: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
        
                // Abrir modal al hacer clic en "Buscar"
                $("#buscar").click(function(event) {
                    event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
                    $("#myModal").show();
                });
        
                // Volver desde el modal
                $("#volver").click(function(event) {
                    event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
                    // Guardar ubicación y cerrar modal
                    ubicacionActual = $("#direccion-modal").val();
                    $("#myModal").hide();
                });
        
                // Inicializar el mapa al hacer clic en "Buscar" dentro del modal
                $("#buscar-modal").click(function(event) {
                    event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
                    var direccionModal = $("#direccion-modal").val();
                    if (direccionModal !== "") {
                        geocodeAndCenter(direccionModal);
                    }
                });
            }); 
        };
        document.head.appendChild(jqueryScript);


        // Crear un elemento de botón
        var buttonElement = document.createElement('button');
        buttonElement.textContent = 'Buscar localización';
        buttonElement.id="buscar";
        

        var nuevoCodigoHTML = `
        <div id="myModal" class="modal">
            <div class="modal-content">
                <h2>Ingrese una dirección</h2>
                <input type="text" id="direccion-modal">
                <div id="mapa-modal"></div>
                <button id="buscar-modal">Buscar</button>
                <button class="modal-button" id="volver">Guardar</button>
            </div>
        </div> 
        `;

       


        // Añadir el botón después del elemento label en el DOM
        labelElement.parentNode.parentElement.insertBefore(buttonElement, labelElement.parentElement);
    
         // Agrega el nuevo código HTML después del botón en el DOM
         buttonElement.insertAdjacentHTML('afterend', nuevoCodigoHTML);

           
    }

          

    
    function geocodeAndCenter(address) {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({'address': address}, function(results, status) {
            if (status === 'OK') {
                var location = results[0].geometry.location;

                // Guardar las coordenadas
                latitud = location.lat();
                longitud = location.lng();
                $("#Item_latitud").val(latitud);
                $("#Item_longitud").val(longitud);


                // Centrar el mapa en la ubicación geocodificada
                mapaModal.setCenter(location);

                // Agregar un marcador en el mapa
                var marker = new google.maps.Marker({
                    map: mapaModal,
                    position: location
                });

                // Cerrar el modal después de geocodificar
                //$("#myModal").hide();
            } else {
                var mensajeError = "";
                if (status === "ZERO_RESULTS") {
                    mensajeError = "No hubo resultados para la dirección ingresada.";
                } else if (status === "OVER_QUERY_LIMIT" || status === "REQUEST_DENIED" || status === "UNKNOWN_ERROR") {
                    mensajeError = "Error general del mapa.";
                } else if (status === "INVALID_REQUEST") {
                    mensajeError = "Error de la web. Contacte con Name Agency.";
                }
                alert(mensajeError);
            }
        });
    }
})
