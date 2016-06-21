<?php if($this->editmode) { ?>

    <?= $this->video('video', [
        'attributes' => [
            'class' => 'video-js vjs-default-skin vjs-big-play-centered',
            'data-setup' => '{}'
        ],
        'thumbnail' => 'content',
        'disableProgressReload' => TRUE,
        'height' => 250
    ]); ?>

<?php } else { ?>

    <?php echo $this->template('toolbox/video/type-' . $this->video('video')->getVideoType() . '.php', [
        'autoplay' => $this->autoplay
    ]) ?>

<?php } ?>