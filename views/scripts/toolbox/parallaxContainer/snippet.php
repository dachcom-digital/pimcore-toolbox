<div class="parallax-container-snippet">

    <div class="background">

        <?php echo $this->snippet("parallaxContainerSnippet", array('reload' => TRUE)); ?>

    </div>

    <div class="content">
        <?= $this->template('helper/areablock.php', [ 'name' => 'parallaxContainerContent', 'excludeBricks' => array('parallaxContainer') ] ); ?>
    </div>

</div>