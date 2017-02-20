<div class="player" data-poster-path="<?= $this->posterPath ?>" data-play-in-lightbox="<?= $this->playInLightbox ?>" data-video-uri="<?= $this->vhs('video')->id ?>"></div>
<?php if(!empty($this->posterPath)) { ?>
    <?= $this->partial('toolbox/video/partial/overlay.php', ['posterPath' => $this->posterPath, 'playInLightbox' => $this->playInLightbox] ); ?>
<?php } ?>
