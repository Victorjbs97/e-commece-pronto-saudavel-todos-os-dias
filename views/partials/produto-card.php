<?php
// Este arquivo assume que uma variável chamada $produto existe.
// O foreach em 'mais-vendidos.php' garante que ela exista.
?>
<article class="product-card">

    <figure class="product-card__figure">
        <img class="product-card__image"
             src="<?= htmlspecialchars($produto['imagem_url']) ?>" 
             alt="<?= htmlspecialchars($produto['nome']) ?>">
        </img>     
    </figure>

    <div class="product-card__content">

        <div class="product-card__tags">
             <span class="tag-item">CONTÉM GLÚTEN</span>

            <span class="tag-item">CONTÉM LACTOSE</span>
        </div>

        <div class="product-card__nutrition">

        <div class="nutrition-item">

                <strong>444</strong>

                <span>KCAL</span>

            </div>

            <div class="nutrition-item">

                <strong>56</strong>

                <span>PROT.</span>

            </div>

            <div class="nutrition-item">

                <strong>120</strong>

                <span>CARB.</span>

            </div>

            <div class="nutrition-item">

                <strong>17</strong>

                <span>GORD.</span>

            </div>
        </div>
        
        <h3 class="product-card__title"><?= htmlspecialchars($produto['nome']) ?></h3>

        <p class="product-card__price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
    </div>

    <div class="product-card__actions">
        <div class="quantity-selector">

            <button class="quantity-selector__button">-</button>

            <input class="quantity-selector__input" type="text" value="1" readonly>

            <button class="quantity-selector__button">+</button>

        </div>
        <button class="button button--primary">

            Adicionar

            <svg class="button__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">

                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>

            </svg>

        </button>
    </div>
    
</article>