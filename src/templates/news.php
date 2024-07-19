<?php

/**
 * @var \Cyclonecode\NewsDataIO\Responses\NewsResponse $result
 */

?>
<div class="news-wrapper">
    <?php /** @var \Cyclonecode\NewsDataIO\Responses\Article $article */ ?>
    <?php foreach ($result->getResults() as $article) : ?>
        <article id="article-<?php echo esc_attr($article->getArticleId()); ?>">
            <h5><?php echo esc_attr($article->getTitle()); ?></h5>
            <!--<span><?php echo esc_attr($article->getPubDate()->format('Y-m-d')); ?></span>-->
            <p class="description">
                <?php if ($article->getImageUrl()) : ?>
                    <img src="<?php echo esc_attr($article->getImageUrl()); ?>" width="240" height="135" alt="<?php echo esc_attr($article->getTitle()); ?>">
                <?php endif; ?>
                <?php echo esc_attr($article->getDescription()); ?></p>
            <a href="<?php echo esc_url($article->getLink()); ?>" target="_blank"><?php esc_attr_e('Read more', 'newsdata-io'); ?></a>
        </article>
    <?php endforeach; ?>
</div>
