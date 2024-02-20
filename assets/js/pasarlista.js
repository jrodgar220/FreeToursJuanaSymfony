
    $(document).ready(function () {
        var tourIdElegido=0;
        // Función para hacer la llamada AJAX y construir el modal
        function cargarModalLista(tourId) {
            $.ajax({
                url: '/tours/asistencia/' + tourId, // URL de la ruta que maneja la obtención de reservas para el tour específico
                method: 'GET',
                success: function (reservas) {
                    // Limpiar la lista de asistentes en el modal
                    $('#listaAsistentes').empty();

                    // Iterar sobre las reservas y agregarlas al modal
                    $.each(reservas, function (index, reserva) {
                        var reservaHtml = '<div class="form-row">' +
                        '<div class="col">' +
                        '<label>' + reserva.usuario.nombre + '</label>' +
                        '</div>' +
                        '<div class="col form-group">' +
                        '<input type="number" class="form-control" name="asistentes[]" value="' + reserva.asistentes + '">' +
                        '<input type="hidden" name="idUsuario[]" value="' + reserva.usuario.id + '">' +
                        '</div>' +
                        '</div>';
                    
                        $('#listaAsistentes').append(reservaHtml);
                    });

                    // Mostrar el modal
                    $('#modalLista').modal('show');
                },
                error: function (xhr, status, error) {
                    // Manejar errores si es necesario
                }
            });
        }

        // Manejar clic en el botón "Pasar lista"
        $('.btn-pasar-lista').click(function () {
            tourIdElegido = $(this).data('idtour');
            cargarModalLista(tourIdElegido);
        });

        // Función para guardar los cambios en la lista de asistentes
        $('#guardarCambios').click(function () {
            var asistentesActualizados = [];

            // Recopilar la información actualizada de asistentes
            $('#listaAsistentes .form-group').each(function () {
                
                var idUsuario = $(this).find('input[name="idUsuario[]"]').val();
                var numAsistentes = $(this).find('input[name="asistentes[]"]').val();
                asistentesActualizados.push({ usuario: idUsuario, asistentes: numAsistentes });
            });

            // Construir el JSON con la información actualizada
            var jsonActualizado = JSON.stringify(asistentesActualizados);
            console.log(jsonActualizado); // Aquí puedes enviar este JSON a través de una solicitud POST AJAX
            
            $.ajax({
                url: '/tours/asistencia/'+tourIdElegido,
                method: 'PUT',
                contentType: 'application/json',
                data: jsonActualizado,
                success: function (response) {
                    // Manejar la respuesta si es necesario
                    alert("Asistencia actualizada")
                    
                    $('.modalLista').modal('hide');
                    window.location.reload();
                },
                error: function (xhr, textStatus, errorThrown) {
                   
                    if (xhr.status == 201) {
                        alert("Asistencia actualizada")
                    
                        $('.modalLista').modal('hide');
                        window.location.reload();
                    }
                }
            });
            

            
        });
    });
