
$(document).ready(function () {

    var tourIdElegido = 0;




    //boton procesar
    $('#procesar').on('click', function () {

        var form_data = new FormData();
        var file_data = $("#foto").prop("files")[0];
        form_data.append("file", file_data);


        var datos = $('#ruta_form').serializeArray();

        var jsonData = {};

        datos.forEach(function (input) {

            jsonData[input.name] = input.value;

        });

        console.log(jsonData); // Puedes hacer lo que quieras con el JSON, por ejemplo, enviarlo por AJAX
        form_data.append("datos", JSON.stringify(jsonData));



        $.ajax({
            cache: false,
            contentType: false,
            dataType: 'JSON',
            enctype: 'multipart/form-data',
            method: "POST",
            url: "./tours/informe/" + idTour,
            dataType: 'json',
            data: form_data,
            processData: false,
            success: function (data) {
                alert("Informe")
            }
        });
    });


    $('.btn-informe').click(function () {
        tourIdElegido = $(this).data('idtour');

        $('#modalInforme').modal('show');
    });

    // Manejar envío del formulario
    $('#formInforme').submit(function (event) {
        event.preventDefault();


        var form_data = new FormData();
        var file_data = $("#archivoInforme").prop("files")[0];
        form_data.append("file", file_data);


        var datos = $('#formInforme').serializeArray();

        var jsonData = {};

        datos.forEach(function (input) {
            jsonData[input.name] = input.value;
        });

        console.log(jsonData); // Puedes hacer lo que quieras con el JSON, por ejemplo, enviarlo por AJAX
        form_data.append("datos", JSON.stringify(jsonData));


        $.ajax({
            cache: false,
            contentType: false,
            dataType: 'JSON',
            enctype: 'multipart/form-data',
            method: "POST",
            url: "/tours/informe/" + tourIdElegido,
            dataType: 'json',
            data: form_data,
            processData: false,
            success: function (data) {
                alert("Informe generado")
                $('#modalInforme').modal('hide');

            },
            error: function (xhr, textStatus, errorThrown) {
               
                if (xhr.status == 201) {
                    alert("Informe generado")
                
                    $('.modalInforme').modal('hide');
                    window.location.reload();
                }
                
            }
        });

    });
});

