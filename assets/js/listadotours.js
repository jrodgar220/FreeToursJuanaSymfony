$(document).ready(function () {


    var tours = [];
    $('.tab-link').click(function () {
        var tabId = $(this).data('tab');
        $('.tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    
   

    var guias = [];
    fetch("./usuarios/guias/")
        .then(response => response.json())
        .then(data => {
            if (data) {
                guias = data;
            }
        });

    var ruta = $("#tour");
    $(".content-body").append(ruta);
    $(".title").append("Listado de tours");

    fetch("./tours/")
        .then(response => response.json())
        .then(data => {
            tours = data;
            const toursPasados = [];
            const toursPendientes = [];

            const currentDate = new Date();
            const today = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());

            data.forEach(item => {
                const tourDate = new Date(item.fecha.date);
                if (tourDate < today) {
                    toursPasados.push(item);
                } else {
                    toursPendientes.push(item);
                }
            });
            toursPasados.sort((a, b) => new Date(a.fecha.date) - new Date(b.fecha.date));

            toursPendientes.sort((a, b) => new Date(a.fecha.date) - new Date(b.fecha.date));

            renderTours("tours-pasados", toursPasados, true);
            renderTours("tours-pendientes", toursPendientes, false);

            
            // Evento de clic en la pestaña de tours
            $(".tab-link").click(function () {
                var tab_id = $(this).attr('data-tab');
                $(".tab-content").removeClass('current');
                $(".tab-link").removeClass('current');

                $(this).addClass('current');
                $("#" + tab_id).addClass('current');
            });
           
        });

    function renderTours(containerId, tours, hanpasado) {
        const toursByDate = {};
        tours.forEach(item => {
            const fecha = item.fecha.date.split(' ')[0];
            if (!toursByDate[fecha]) {
                toursByDate[fecha] = [];
            }
            toursByDate[fecha].push(item);
        });


        let html = '';
        for (const fecha in toursByDate) {

            html += `<h4 class="fecha-header">${fecha}<i class="fas fa-angle-double-down dropdown"></i></h4>`;
            html += `<div class="table-container" style="display: none;">`; // Agregar contenedor para la tabla y ocultarlo inicialmente
            html += '<table class="table datagrid">';
            html += `<thead>
                            <tr>
                                <th class="header-for-field-text text-">Titulo</th>
                                <th class="header-for-field-text text-">Descripcion</th>
                                <th class="header-for-field-text text-">Hora</th>
                                <th class="header-for-field-text text-">Guia</th>`
            if (!hanpasado) 
                html += `<th></th>`
            html +=`<th class="header-for-field-text text-">Asistentes</th>`
                html += `<th></th>`

            
            
            
            if (hanpasado) {
                html +=` <th class="header-for-field-text text-">Puntuación guía</th>`

                html += `<th></th>`
            }
           
            html += `</tr></thead>
                        <tbody>`;
            toursByDate[fecha].forEach((tour, index, toursArray) => {
                const valoracionesValidas = tour.reservas
                    .filter(reserva => reserva.valoracion && reserva.valoracion.puntuacionguia !== null)
                    .map(reserva => reserva.valoracion.puntuacionguia);

                // Calcular la puntuación media del guía en la ruta
                let puntuacionMedia = 0;
                if (valoracionesValidas.length > 0) {
                    puntuacionMedia = valoracionesValidas.reduce((total, puntuacion) => total + puntuacion, 0) / valoracionesValidas.length;
                }

                const hora = tour.hora.date.split(' ')[1].slice(0, 5);
                let iconowarning = '';
                let actionsHtml = '';
                if (!hanpasado) {
                    const sameTimeTours = toursArray.filter(t => t.hora.date.split(' ')[1].slice(0, 5) === hora && t.guia.nombre === tour.guia.nombre);

                    if (sameTimeTours.length > 1) {
                        iconowarning = '<i class="fas fa-exclamation-triangle warning"></i>';
                    }
                    actionsHtml += `<td><button class="edit-btn">Editar</button>
                                            <button class="save-btn" style="display:none">Guardar</button></td>`;
                }
                html += `<tr id=${tour.id}>
                                        <td data-label="Titulo">${tour.ruta.titulo}</td>
                                        <td data-label="Descripción">${tour.ruta.descripcion}</td>
                                        <td data-label="Hora">${hora}</td>
                                        <td data-label="Guía">${tour.guia.nombre}${iconowarning}</td>
                                        ${actionsHtml}`
                let warningasistentes = '';

                if (!tour.cancelado && !hanpasado) {
                    if (tour.asistentes < 4)
                        warningasistentes += '<i class="fas fa-exclamation-triangle warningred"></i>'; 
                }                      
                html += ` <td data-label="Asistentes">${tour.asistentes}${warningasistentes}</td><td>  `
                if (!tour.cancelado && !hanpasado) {
                    html += `<button class="cancel-btn">Cancelar</button>`
                }else if (tour.cancelado && !hanpasado) {
                    html += `<span class="error">El tour ha sido cancelado</span>`
                }

                if(hanpasado){
                    html += `</td><td>${puntuacionMedia}</td>`
                } 
                                 
                if (hanpasado ){
                    html += `<td>`
                    if(tour.informe !== null)
                        html += `<button id="verInformeBtn_${tour.id}" class="verInformeBtn" >Ver Informe</button>`
                    html+=`</td>`
                }
                                  
                html += "</tr>";

            });

            html += '</tbody></table>';
            html += `</div>`; // Cerrar el contenedor de la tabla


        }

        document.getElementById(containerId).innerHTML = html;

        // Agregar evento de clic a los headers de fecha
        $(`#${containerId} .fecha-header`).click(function () {
            // Encuentra el contenedor de la tabla correspondiente
            var tableContainer = $(this).next('.table-container');
            // Alternar la visibilidad de la tabla con una animación
            tableContainer.slideToggle();
        });

        document.querySelectorAll(`#${containerId} .edit-btn`).forEach(button => {
            button.addEventListener('click', function () {
                var $row = $(this).closest('tr');
                var $guiaCell = $row.find('td:nth-child(4)'); // Ajusta este selector según la posición de la celda
                var originalGuia = $guiaCell.text();
                var $select = $('<select>');
                for (var i = 0; i < guias.length; i++) {
                    $select.append($('<option>', {
                        value: guias[i].id,
                        text: guias[i].nombre
                    }));
                }
                $guiaCell.html($select);
                $(this).hide();
                $row.find('.save-btn').show();
            });

        });

        // Evento de clic en el botón de guardar
        $(document).on('click', `#${containerId} .save-btn`, function () {
            var $botonguardar = $(this);
            var $row = $(this).closest('tr');
            var $guiaCell = $row.find('td:nth-child(4)'); // Ajusta este selector según la posición de la celda
            var $select = $guiaCell.find('select');
            var newGuia = $select.find('option:selected').text()
            $guiaCell.html(newGuia);
            var datos = {
                tour: $row.attr('id'),
                guia: $select.val()
            }
            $.ajax({

                dataType: 'JSON',
                method: "PUT",
                url: "./tours/asignarguia",
                dataType: 'json',
                data: JSON.stringify(datos),
                processData: false,
                success: function () {
                    $row.find('.edit-btn').show();
                    $botonguardar.hide();
                }
            });


        });


        // Evento de clic en el botón de cancelar
        $(document).on('click', `#${containerId} .cancel-btn`, function () {

            var $row = $(this).closest('tr');

            $.ajax({

                dataType: 'JSON',
                method: "PUT",
                url: "./tours/cancelar/" + $row.attr('id'),
                dataType: 'json',
                processData: false,
                success: function () {
                    alert("Tour cancelada")
                }
            });


        });
    }

    $('#tour').on('click', `.verInformeBtn`, function () {
        const tourId = $(this).attr('id').split('_')[1];
        const tour = obtenerTourPorId(tourId); // Suponiendo que tienes una función para obtener el objeto tour por su ID
        if (tour && tour.informe) {
            $('#informeInfo').html(`
                <p><strong>Observaciones:</strong> ${tour.informe.observaciones}</p>
                <p><strong>Dinero Recaudado:</strong> ${tour.informe.dinerorecaudado} €</p>
                <img src="./uploads/informes/${tour.informe.foto}" class="img-responsive">
            `);
            $('#informeModal').css('display', 'block');
        }
    });

    $('.close').on('click', function() {
        $('#informeModal').css('display', 'none');
    });

    function obtenerTourPorId(id) {
        // Iterar sobre la lista de tours
        for (let i = 0; i < tours.length; i++) {
            // Si se encuentra un tour con el ID proporcionado, devolverlo
            if (tours[i].id == id - 0) {
                return tours[i];
            }
        }
        // Si no se encuentra ningún tour con el ID proporcionado, devolver null
        return null;
    }
});
