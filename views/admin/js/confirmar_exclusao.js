'use strict';

const links = document.querySelectorAll('.excluir');

for (const link of links) {
    link.addEventListener("click", function(event) {
        // Anular o comportamento padrão do evento
        event.preventDefault();

        let resposta = confirm("⚠️ Atenção! Você está prestes a excluir este produto do banco de dados e do site. Essa ação é irreversível. Deseja continuar?");

        /* Se a resposta for TRUE */
        if(resposta){
            // Redirecionamos para o endereço (href) do link
            location.href= link.href; 
        }
    });
}
