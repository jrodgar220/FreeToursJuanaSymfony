
    $(document).ready(function() {
       
        $('.datepicker').datepicker({
            dateFormat: 'dd/mm/yy', // Formato de fecha
        });

        
   
        // Función para extraer labels
        function extractLabels(data) {
            const labels = [];

            function extractLabelsRecursive(item) {
                labels.push(item.label); // Agrega el label de la comunidad

                if (item.provinces) {
                item.provinces.forEach((province) => {
                    labels.push(province.label); // Agrega el label de la provincia

                    if (province.towns) {
                    province.towns.forEach((town) => {
                        labels.push(town.label); // Agrega el label de la población
                    });
                    }
                });
                }
            }

            data.forEach(extractLabelsRecursive);
            return labels;
        }
        const apiUrl = './arbol.json';

        //obtener localidades
        fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            var select2script = document.createElement('script');
            select2script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            document.head.appendChild(select2script);
            select2script.onload = function() {
                const allLabels = extractLabels(data);
                $("#autocompleteSelect").select2({
                    data: allLabels,
                    tags: true,
                    tokenSeparators: [',', ' '],
                    width: '100%',
                });
            }
            
            
            
           
        });

        
        
        
       
        
        //boton buscar
        $('#buscar').on('click', function() {
            const inputFecha = document.getElementById("fecha").value;
            const inputLocalidad = document.getElementById("autocompleteSelect").value;
            var datos = {
                fecha:inputFecha,
                localidad: inputLocalidad,

            };



            $.ajax({
                cache: false,
                contentType: false,
                dataType: 'JSON',
                method: "POST",
                url: "./rutas/obtener",
                dataType: 'json',
                data: JSON.stringify(datos),
                processData: false,
                success: function (data) {
                    cargarDatos(data);
                }
              });
        });


        function cargarDatos(lista){
            const inputFecha = document.getElementById("fecha").value;
            rutas= Object.entries(lista);
            var nuevoHTML="";
            var contenedor = document.getElementById('listarutas'); // Reemplaza '.container' con el selector adecuado
            rutas.forEach(function(ruta) {
                var clasecancelado="";
                var disponibilidad=20-ruta[1].asistentes;
                if(ruta[1].cancelado)
                    clasecancelado="cancelado";
                nuevoHTML += `
                    <div class="tour-card ${clasecancelado}">
                        <div class="tour-card__container " >
                            <div class="col-md-3">
                                <a href="#" class="tour-card__image tour-card__image--new">
                                    <img src="/uploads/rutas/${ruta[1].foto}">
                                </a>
                            </div>
                            <div>
                                <div class="tour-card__name">
                                    <span class="tour-card__title">${ruta[1].titulo}</span>
                                </div>
                                <div class="tour-card__text">
                                ${ruta[1].descripcion}`;
                                if(ruta[1].cancelado){
                                    nuevoHTML += `<br>
                                    <strong>El tour ha sido cancelado </strong> `
                                }else{
                                    if(disponibilidad>0){
                                        nuevoHTML += `<br>
                                        <strong>Quedan ${disponibilidad} plazas!</strong>`
                                    }else{
                                        nuevoHTML += `<br>
                                        <strong>No quedan plazas plazas!</strong>`
                                    }
                                    
                                }
                               
                                    
                                
                                nuevoHTML += `  </div>
                                <div class="tour-card__bottom">
                                    <a href="./rutas/${ruta[1].id}/?fecha=${inputFecha}" class="tour-card__details" style="text-transform: none;">
                                        Info y Reservas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        // Agregar el nuevo elemento al contenedor
        contenedor.innerHTML= nuevoHTML;
        }
    });


