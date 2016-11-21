<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('teaser', $this) ?>
<?php }?>
<div class="toolbox-teaser <?= $this->select('teaserContainerAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/teaser.php') ?>
</div>