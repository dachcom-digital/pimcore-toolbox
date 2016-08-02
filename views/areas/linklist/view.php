<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('linklist', $this) ?>
<?php }?>
<div class="toolbox-linklist <?= $this->select('linklistAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/linklist.php') ?>
</div>