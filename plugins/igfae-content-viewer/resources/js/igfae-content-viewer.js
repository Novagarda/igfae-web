jQuery(document).ready(function($) {
/*
    $("#igfae-search-form input[type='submit']").on("click", function(event) {
        sendForm( $($('#igfae-search-form')[0]) , new FormData($('#igfae-search-form')[0]) );
        event.preventDefault();
    });

    $("#igfae-search-results").on("click", "#igfae-pagination li.disabled > a", function(event) {
        event.preventDefault();
    });

    $("#igfae-search-results").on("click", "#igfae-pagination li:not(.disabled) > a", function(event) {
        //event.preventDefault();
        var page = $($(this)[0]).data('page');

        var formData = new FormData( $("#igfae-search-form")[0] );

        formData.append('igfae-page', page);

        sendForm( $($("#igfae-search-form")[0]) , formData );
    });

    function sendForm(form, data) {

    	$.ajax({
            //dataType: "json",
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,        // To send DOMDocument or non processed data file it is set to false
            type: form.attr("method"),
            url: form.attr("action"),
            data: data,
            
            beforeSend: function(jqXHR, settings) {
                onFormBeforeSend(form, jqXHR, settings);
            },
            
            success: function(response, textStatus, jqXHR) {
                onFormSuccess(form, response, textStatus, jqXHR);
            },
            
            error: function(jqXHR, textStatus, errorThrown) {
                onFormError(form, jqXHR, textStatus, errorThrown);
            },
            
            complete: function(jqXHR, textStatus) {
                onFormComplete(form, jqXHR, textStatus);
            }
        });
    }

    function onFormBeforeSend(form, jqXHR, settings) {
    	//console.log('onFormBeforeSend: '+settings);
        $('#loader').toggleClass('hidden');
        $('#igfae-search-results').toggleClass('hidden');
    }

    function onFormSuccess(form, response, textStatus, jqXHR){
    	$('#igfae-search-results').empty();
    	$('#igfae-search-results').append(response);
    }

    function onFormError(form, jqXHR, textStatus, errorThrown) {
    	//console.log('onFormError: '+textStatus);
    }

    function onFormComplete(form, jqXHR, textStatus) {
        setTimeout(function(){
            $('#loader').toggleClass('hidden');
            $('#igfae-search-results').toggleClass('hidden');
        }, 250);
    }
*/
});

function abrirEmerxente(){
    const btnClicado = event.target;
    console.log(btnClicado);
    var idPublicacion = btnClicado.dataset.index;
    var destino = 'emerxente-' + idPublicacion;
    console.log(destino);
    var ventaPublicacion = document.getElementById(destino);
    ventaPublicacion.classList.add("ver");

    const btnVerPublicacion = document.getElementsByClassName('ver-emerxente');
    if(btnVerPublicacion){

        for (let k = 0; k < btnVerPublicacion.length; k++) {

            var idPub = btnVerPublicacion[k].dataset.index;

            if(idPub != idPublicacion){
                var dest = 'emerxente-' + idPub;
                var ventaPub = document.getElementById(dest);
                if (ventaPub.classList.contains('ver')) {
                    ventaPub.classList.remove("ver");
                }
            }                  

        }
            
        const btnCerrarPublicacion =  document.getElementsByClassName('close-publicacion-emerxente');
        if(btnCerrarPublicacion){
            for (let i = 0; i < btnCerrarPublicacion.length; i++) {
                btnCerrarPublicacion[i].addEventListener("click", function() {
                var idPublicacionCerrar = btnVerPublicacion[i].dataset.index;
                var destinoCerrar = 'emerxente-' + idPublicacionCerrar;
                var ventaPublicacionCerrar = document.getElementById(destinoCerrar);
                ventaPublicacionCerrar.classList.remove("ver");
                });
            }
        }
    }
}