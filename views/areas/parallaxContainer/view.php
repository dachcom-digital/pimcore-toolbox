<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('parallaxContainer', $this) ?>
<?php }?>
<div class="toolbox-parallax-container <?= $this->select('parallaxContainerAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/parallaxContainer.php') ?>
</div>