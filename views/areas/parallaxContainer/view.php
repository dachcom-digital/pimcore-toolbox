<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('parallaxContainer', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-parallax-container parallax-section <?= $this->select('parallaxContainerAdditionalClasses')->getData(); ?>">

    <?= $this->template('toolbox/parallaxContainer.php', [
        'containerWrapper'   => $this->select('containerType')->getData(),
        'parallaxBackground' => $this->href('background'),
        'parallaxBehind'     => $this->parallaximage('imagesBehind'),
        'parallaxFront'      => $this->parallaximage('imageFront')
    ]) ?>

</div>