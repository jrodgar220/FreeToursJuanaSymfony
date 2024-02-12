$(document).ready(function() {
   
    var botones='<div class="global-actions d-block"><a class=" action-new btn btn-primary" href="http://localhost:8000/admin?routeName=vista_form_ruta" data-action-name="new"><span class="action-label">Crear ruta</span></a> </div>'
	
    var ruta = $("#ruta");
    $(".content-body").append(ruta);
    $(".title").append ("Listado de rutas");
    $(".page-actions").append(botones);
});