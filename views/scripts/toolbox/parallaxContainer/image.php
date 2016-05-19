<div class="parallax-container-image">

    <?php if($this->editmode) { ?>
        <div class="editmode-parallax-container-image">
            <?= $this->image("parallaxContainerImage", [

                "thumbnail" => [
                    "width" => 200,
                    "height" => 100,
                    "interlace" => true,
                    "quality" => 90
                ],
                "width" => 200,
                "height" => 100,
                "reload"    => true,
                "class"     => "img-responsive",
                "dropClass" => "canvas",
                "title"     => "Bild hierherziehen"
            ]);
            ?>
        </div>
    <?php } ?>

    <div class="content">
        <?= $this->template('helper/areablock.php', [ 'name' => 'parallaxContainerContent', 'type' => 'image' ] ); ?>
    </div>

    <div class="background">

        <?php $thumbnail = $this->image("parallaxContainerImage")->getThumbnail("parallaxContainerImage"); ?>
        <div class="canvas" data-width="1024" data-height="768" style="background-image:url('<?= $thumbnail; ?>');"></div>

    </div>

</div>