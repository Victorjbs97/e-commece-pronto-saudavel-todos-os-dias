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
        <form action="<?= $baseUrl ?>/public/index.php" method="POST">
            
            <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
            
            <input type="hidden" name="action" value="gerenciar_carrinho">
            
            <div class="quantity-selector">
                <button class="quantity-selector__button" type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                <input class="quantity-selector__input" type="number" name="quantidade" value="1" min="1" class="input-quantidade">
                <button class="quantity-selector__button" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
            </div>

            <button type="submit" class="button button--primary">Adicionar</button>
            
        </form>
    </div>
    
</article>

