<?php if ($this->editmode && $this->toolboxHelper()->hasAdditionalClasses('video')) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Zusatz:</label>
        </div>
        <div class="form-group">

            <?php

            $acStore = $this->toolboxHelper()->getConfigArray( 'video/additionalClasses', TRUE );
            echo $this->select('videoAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
            ?>

        </div>

    </div>

<?php } ?>
<div class="toolbox-video <?= $this->select('videoAdditionalClasses')->getData();?>">

    <?= $this->video("video", [
        "attributes" => [
            "class" => "video-js vjs-default-skin vjs-big-play-centered",
            "data-setup" => "{}"
        ],
        "thumbnail" => "content",
        "height" => 250
    ]); ?>

</div>