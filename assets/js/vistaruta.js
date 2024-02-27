    $(document).ready(function(){

        $('.btn-reservar').on('click', function() {

            var idTour = $(this).data('tour-id');
            var idInputAsistentes= '#numeroAsistentes'+idTour;
            var numAsistentes = $(idInputAsistentes).val();
            if (numAsistentes > 6) {
                showAlert('El máximo de asistentes permitidos es 6.', 2000)

                
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
                    showAlert('Reserva creada correctamente', 2000)
                    setTimeout(function() {
                        $('.modalReserva').modal('hide');
                    }, 500);; 

                },
                error: function(xhr, textStatus, errorThrown) {
                    if (xhr.status == 409) {
                        showAlert(' Ya has realizado una reserva para este tour', 2000)

                    }
                    if (xhr.status == 200) {
                        showAlert(' Reserva creada', 2000)
                    }
                    setTimeout(function() {
                        $('.modalReserva').modal('hide');
                    }, 500);
                }

                
            });
        });

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
    })