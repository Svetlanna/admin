<article>
    <a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
    <div class="anons">
        <?php the_truncated_post( 500 ); ?>
    </div>
    <a href="<?php the_permalink(); ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Скачать</a>
</article>