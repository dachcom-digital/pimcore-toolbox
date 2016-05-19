<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('separator', $this) ?>
<?php }?>
<div class="toolbox-separator <?= $this->select('separatorContainerAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/separator.php') ?>
</div>