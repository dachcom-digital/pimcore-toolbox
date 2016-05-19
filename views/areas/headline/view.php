<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('headline', $this) ?>
<?php }?>
<div class="toolbox-headline <?= $this->select('headlineAdditionalClasses')->getData();?>">
    <?= $this->template("toolbox/headline.php") ?>
</div>