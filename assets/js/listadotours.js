$(document).ready(function () {

    var guias = [];
    fetch("./usuarios/guias/")
        .then(response => response.json())
        .then(data => {
            if (data) {
                guias = data;
            }
        })

    var ruta = $("#tour");
    $(".content-body").append(ruta);
    $(".title").append("Listado de tours");

    fetch("./tours/")
        .then(response => response.json())
        .then(data => {
            const toursByDate = {};
            data.forEach(item => {
                const fecha = item.fecha.date.split(' ')[0];
                if (!toursByDate[fecha]) {
                    toursByDate[fecha] = [];
                }
                toursByDate[fecha].push(item);
            });
            let html = '';
            for (const fecha in toursByDate) {
                html += `<h2 class="fecha-header">${fecha}</h2>`;
                html += `<div class="table-container" style="display: none;">`; // Agregar contenedor para la tabla y ocultarlo inicialmente
                html += '<table class="table datagrid">';
                html += `<thead>
                            <tr>
                                <th class="header-for-field-text text-">Titulo</th>
                                <th class="header-for-field-text text-">Descripcion</th>
                                <th class="header-for-field-text text-">Hora</th>
                                <th class="header-for-field-text text-">Guia</th>
                                <th></th><th></th>
                            </tr>
                        </thead>
                        <tbody>`;
                toursByDate[fecha].forEach((tour, index, toursArray) => {
                    const hora = tour.hora.date.split(' ')[1].slice(0, 5);
                    let actionsHtml = '<td>';
                    const sameTimeTours = toursArray.filter(t => t.hora.date.split(' ')[1].slice(0, 5) === hora && t.guia.nombre === tour.guia.nombre);
                    if (sameTimeTours.length > 1) {
                        actionsHtml += '<i class="fas fa-exclamation-triangle warning"></i>';
                    }
                    actionsHtml += `<button class="edit-btn">Editar</button>
                                    <button class="save-btn" style="display:none">Guardar</button></td>`;

                    html += `<tr id=${tour.id}>
                                <td data-label="Titulo">${tour.ruta.titulo}</td>
                                <td data-label="Descripción">${tour.ruta.descripcion}</td>
                                <td data-label="Hora">${hora}</td>
                                <td data-label="Guía">${tour.guia.nombre}</td>
                                ${actionsHtml}
                                <td>`
                    if (!tour.cancelado){
                        html +=` <button class="cancel-btn">Cancelar</button>`
                    }
                    html +=`</td></tr>`;
                });
                html += '</tbody></table>';
                html += `</div>`; // Cerrar el contenedor de la tabla
            }

            document.getElementById('tours').innerHTML = html;

            // Agregar evento de clic a los headers de fecha
            $('.fecha-header').click(function () {
                // Encuentra el contenedor de la tabla correspondiente
                var tableContainer = $(this).next('.table-container');
                // Alternar la visibilidad de la tabla con una animación
                tableContainer.slideToggle();
            });

            document.querySelectorAll('.edit-btn').forEach(button => {
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
            $(document).on('click', '.save-btn', function () {
                var $botonguardar= $(this);
                var $row = $(this).closest('tr');
                var $guiaCell = $row.find('td:nth-child(4)'); // Ajusta este selector según la posición de la celda
                var $select = $guiaCell.find('select');
                var newGuia = $select.find('option:selected').text()
                $guiaCell.html(newGuia);
                var datos = {
                    tour:  $row.attr('id'),
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
            $(document).on('click', '.cancel-btn', function () {
                
                var $row = $(this).closest('tr');
                   
                $.ajax({
                   
                    dataType: 'JSON',
                    method: "PUT",
                    url: "./tours/cancelar/"+$row.attr('id'),
                    dataType: 'json',
                    processData: false,
                    success: function () {
                       alert("Tour cancelada")                    
                    }
                });

                
            });
        });
});
