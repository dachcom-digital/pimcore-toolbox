<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('columns', $this) ?>
<?php }?>
<?php
$equalHeight = $this->checkbox('equalHeight')->isChecked() && !$this->editmode;
?>
<div class="toolbox-columns <?= $this->select('columnsAdditionalClasses')->getData();?><?= $equalHeight ? ' equal-height' : ''?>">
    <?= $this->template('toolbox/columns.php') ?>
</div>