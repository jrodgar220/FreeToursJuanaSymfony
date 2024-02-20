    $(document).ready(function(){

        $('.btn-reservar').on('click', function() {

            var idTour = $(this).data('tour-id');
            var numAsistentes = $('#numeroAsistentes').val();
            if (numAsistentes > 6) {
                alert('El máximo de asistentes permitidos es 6.');
                return; // Detener la ejecución si el número de asistentes es mayor que 6
            }
        
            var jsonData = {
                asistentes: numAsistentes
            };

            $.ajax({
                method: "POST",
                url: "/reservas/crear/"+idTour,
                data: jsonData,
                success: function (data) {
                    alert("Reserva creada");
                    setTimeout(function() {
                        $('.modalReserva').modal('hide');
                    }, 500);; 

                },
                error: function(xhr, textStatus, errorThrown) {
                    if (xhr.status == 409) {
                        alert("Error: Conflicto en la reserva. Ya has realizado una reserva para este tour.");
                    }
                    if (xhr.status == 200) {
                        alert("Reserva creada");
                    }
                    setTimeout(function() {
                        $('.modalReserva').modal('hide');
                    }, 500);
                }

                
            });
        });
    })