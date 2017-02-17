<?php if($this->editmode) { ?>

    <?= $this->vhs('video', [
        'attributes' => [
            'class' => 'video-js vjs-default-skin vjs-big-play-centered',
            'data-setup' => '{}'
        ],
        'thumbnail' => 'content',
        'disableProgressReload' => TRUE,
        'height' => 250
    ]); ?>

<?php } else { ?>

    <?= $this->template('toolbox/video/type-' . $this->videoType . '.php') ?>

<?php } ?>