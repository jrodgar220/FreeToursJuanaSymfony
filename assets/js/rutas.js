
$(document).ready(function () {
    var ruta = $("#ruta");


    $(".content").append(ruta);
    $('.datepicker').datepicker({
        dateFormat: 'dd/mm/yy', // Formato de fecha
    });

    var botones = '<div class="global-actions d-block"><a id="procesar" class=" action-new btn btn-primary" data-action-name="new"><span class="action-label">Guardar</span></a> </div>'

    $(".title").append("Nueva ruta");
    $(".page-actions").append(botones);

    var guias = [];
    function agregarGuiasASelect(select) {
        // Limpiar opciones existentes
        select.empty();

        // Agregar una opción predeterminada
        select.append($('<option>', {
            value: '',
            text: 'Selecciona una opción'
        }));

        // Agregar las opciones desde el array
        guias.forEach(function (opcion) {
            select.append($('<option>', {
                value: opcion.id,
                text: opcion.email
            }));
        });
    }


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
            select2script.onload = function () {
                const allLabels = extractLabels(data);

                $("#autocompleteSelect").select2({
                    data: allLabels,
                    tags: true,
                    tokenSeparators: [',', ' '],
                    width: '100%',
                });
            }




        })
        .catch(error => {
            console.error('Error al obtener datos:', error);
        });

    //obtener guías
    fetch("./usuarios/guias/")
        .then(response => response.json())
        .then(data => {
            if (data) {
                guias = data;
                var nuevoSelect = $('#programacion-container').find('tr:last-child #selectGuias');
                agregarGuiasASelect(nuevoSelect);
            }
        })
        .catch(error => {
            console.error("Error al procesar la solicitud:", error);
        });

    //obtener items
    fetch("./items/")
        .then(response => response.json())
        .then(data => {
            if (data) {
                items = data;
                console.log(items);
                agregarItems();
            }
        })
        .catch(error => {
            console.error("Error al procesar la solicitud:", error);
        });



    // Agregar lógica para agregar dinámicamente campos de entrada para las horas
    $('#agregar-hora').on('click', function () {
        var newRow = '<tr>';
        newRow += '<td><input type="time" name="ruta[programacion][lunes][]" class="form-control" placeholder="Hora"></td>';
        newRow += '<td><input type="time" name="ruta[programacion][martes][]" class="form-control" placeholder="Hora"></td>';
        newRow += '<td><input type="time" name="ruta[programacion][miercoles][]" class="form-control" placeholder="Hora"></td>';
        newRow += '<td><input type="time" name="ruta[programacion][jueves][]" class="form-control" placeholder="Hora"></td>';
        newRow += '<td><input type="time" name="ruta[programacion][viernes][]" class="form-control" placeholder="Hora"></td>';
        newRow += '<td><input type="time" name="ruta[programacion][sabado][]" class="form-control" placeholder="Hora"></td>';
        newRow += '<td><input type="time" name="ruta[programacion][domingo][]" class="form-control" placeholder="Hora"></td>';
        newRow += '<td><select name="guia" id="selectGuias" class="form-control"></select></td>'
        newRow += '</tr>';
        $('#programacion-container').append(newRow);
        // Obtener el último select agregado y agregar las guías
        var nuevoSelect = $('#programacion-container').find('tr:last-child #selectGuias');
        agregarGuiasASelect(nuevoSelect);
    });



    //boton procesar
    $('#procesar').on('click', function () {
        var emptyFields = $('#ruta_form input[required]').filter(function () {
            return $(this).val().trim() === '';
        });

        if (emptyFields.length > 0) {
            showAlert('Por favor, completa todos los campos obligatorios.',3000)
            //alert('Por favor, completa todos los campos obligatorios.');
            return;
        }

        // Check if there is a guide assigned for each day in the schedule
        var guiasAsignados = $('#programacion-container tr').find('select[name="guia"]').map(function () {
            return $(this).val().trim() !== '';
        }).get();

        if (guiasAsignados.indexOf(false) !== -1) {
            showAlert('Por favor, asigna un guía para cada día en la programación.',3000)

            //alert('Por favor, asigna un guía para cada día en la programación.');
            return;
        }
        var form_data = new FormData();
        var file_data = $("#foto").prop("files")[0];
        form_data.append("file", file_data);


        var datos = $('#ruta_form').serializeArray();

        var jsonData = {};

        datos.forEach(function (input) {
            var regex = /ruta\[programacion\]\[(\w+)\]\[\]/;
            var match = input.name.match(regex);

            var regexGuia = /guia/;
            var matchGuia = input.name.match(regexGuia);

            var regexItem = /item/;
            var matchItem = input.name.match(regexItem);


            if (match) {
                var day = match[1];
                if (!jsonData[day]) {
                    jsonData[day] = [];
                }
                jsonData[day].push(input.value);
            }
            else
                if (matchGuia) {
                    if (!jsonData['guias']) {
                        jsonData['guias'] = [];
                    }
                    jsonData['guias'].push(input.value);
                }
                else
                    if (matchItem) {
                        if (!jsonData['items']) {
                            jsonData['items'] = [];
                        }
                        jsonData['items'].push(input.value);
                    }
                    else {
                        jsonData[input.name] = input.value;
                    }
        });

        console.log(jsonData); // Puedes hacer lo que quieras con el JSON, por ejemplo, enviarlo por AJAX
        form_data.append("datos", JSON.stringify(jsonData));



        $.ajax({
            cache: false,
            contentType: false,
            dataType: 'JSON',
            enctype: 'multipart/form-data',
            method: "POST",
            url: "./rutas/",
            dataType: 'json',
            data: form_data,
            processData: false,
            success: function (data) {
                showAlert('Ruta creada correctamente', 2000)
                window.location.href = 'http://localhost:8000/admin?routeName=vista_lista_ruta';

            },
            error: function (xhr, textStatus, errorThrown) {
               
                if (xhr.status == 201) {
                    showAlert('Ruta creada correctamente', 2000)
                    window.location.href = 'http://localhost:8000/admin?routeName=vista_lista_ruta';
                }
            }
        });
    });


    // Ocultar la lista de autocompletado y la información del elemento cuando se hace clic fuera del área
    $(document).on("click", function (event) {
        if (!$(event.target).closest("#autocomplete").length) {
            $("#searchInput").value = "";
            $("#autocompleteList").hide();
        }
    });

    function agregarItems() {
        const searchInput = $("#searchInput");
        const autocompleteList = $("#autocompleteList");

        const selectedList = $("#selectedList");

        searchInput.on("input", function () {


            const searchTerm = this.value.toLowerCase();
            // Ocultar la lista de autocompletado y la información del elemento cuando el campo de búsqueda está vacío
            if (searchTerm === "") {
                autocompleteList.hide();
            }
            const matches = items.filter(item => item.titulo.toLowerCase().includes(searchTerm));


            // Mostrar la lista de autocompletado
            autocompleteList.html("");
            $.each(matches, function (_, item) {
                const autocompleteItem = $("<div class='autocompleteItem'></div>")
                    .text(item.titulo)
                    .on("click", function () {
                        showItemInfo(item);
                    });
                autocompleteList.append(autocompleteItem);
            });
            // Mostrar la lista de autocompletado cuando hay coincidencias
            autocompleteList.show();
        });

        function showItemInfo(item) {

            // Ruta de la imagen (ajustar según la estructura de tus archivos)
            const imagePath = `./uploads/sitios/${item.foto}`;



            // Agregar el elemento a la lista de seleccionados
            const selectedItem = $(`<li id='${item.id}' class='selectedItem'></li>`);
            selectedItem.append(`<img src="${imagePath}" alt="${item.titulo}">`);
            selectedItem.append(`<input type="hidden" name="item" value="${item.id}">`);
            selectedItem.append(`<span>${item.titulo}</span><br>`);
            const removeButton = $("<span class='removeItem'>Eliminar</span>").on("click", function () {
                selectedItem.remove();
            });
            selectedItem.append(removeButton);
            selectedList.append(selectedItem);
            searchInput.value = "";
            // Ocultar la lista de autocompletado cuando se selecciona un elemento
            autocompleteList.hide();
        }
    }


    var jqueryScript = document.createElement('script');
    jqueryScript.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js';
    document.head.appendChild(jqueryScript);
    jqueryScript.onload = function () {
        // Inicializar el mapa en el modal
        mapaModal = new google.maps.Map(document.getElementById("mapa-modal"), {
            zoom: 16,
            scrollwheel: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // Abrir modal al hacer clic en "Buscar"
        $("#buscar").click(function (event) {
            event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
            $("#myModal").show();
        });

        // Volver desde el modal
        $("#volver").click(function (event) {
            event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
            // Guardar ubicación y cerrar modal
            ubicacionActual = $("#direccion-modal").val();
            $("#myModal").hide();
        });

        $("#close-modal").click(function (event) {
            event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
            $("#myModal").hide();
        });


        // Inicializar el mapa al hacer clic en "Buscar" dentro del modal
        $("#buscar-modal").click(function (event) {
            event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
            var direccionModal = $("#direccion-modal").val();
            if (direccionModal !== "") {
                geocodeAndCenter(direccionModal);
            }
        });
    }

    function geocodeAndCenter(address) {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({ 'address': address }, function (results, status) {
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




    function showAlert(message, timeout) {
        // Muestra el modal
        $('#alertMessage').text(message);
        $('#customAlert').css('display', 'block');
      
        // Cierra automáticamente después de 'timeout' milisegundos
        setTimeout(function() {
          $('#customAlert').css('display', 'none');
        }, timeout);
      }
      
      // Cierra el modal cuando se hace clic en el botón de cerrar
      $('.close').on('click', function() {
        $('#customAlert').css('display', 'none');
      });
      

});
