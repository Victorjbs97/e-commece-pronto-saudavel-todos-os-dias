// Aguarda o documento HTML ser completamente carregado
document.addEventListener('DOMContentLoaded', function() {

    // Seleciona os elementos do nosso carrossel
    const carousel = document.getElementById('mais-vendidos-carousel');
    const prevButton = document.getElementById('mais-vendidos-prev');
    const nextButton = document.getElementById('mais-vendidos-next');

    // Se algum dos elementos não for encontrado, o script para para evitar erros.
    if (!carousel || !prevButton || !nextButton) {
        return;
    }

    // Calcula o quanto rolar com base na largura de um card + o espaçamento
    const firstCard = carousel.querySelector('.product-card');
    if (!firstCard) return; // Se não houver cards, não faz nada

    const gap = parseFloat(getComputedStyle(carousel).gap); // Pega o valor do 'gap' do CSS
    const scrollAmount = firstCard.offsetWidth + gap;

    // Adiciona o evento de clique ao botão "Próximo"
    nextButton.addEventListener('click', () => {
        carousel.scrollBy({
            left: scrollAmount,
            behavior: 'smooth' // Faz a rolagem ser animada
        });
    });

    // Adiciona o evento de clique ao botão "Anterior"
    prevButton.addEventListener('click', () => {
        carousel.scrollBy({
            left: -scrollAmount, // Rola para a esquerda (valor negativo)
            behavior: 'smooth'
        });
    });

});