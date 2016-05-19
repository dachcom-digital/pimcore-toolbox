<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('columns', $this) ?>
<?php }?>
<div class="toolbox-columns <?= $this->select('columnsAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/columns.php') ?>
</div>