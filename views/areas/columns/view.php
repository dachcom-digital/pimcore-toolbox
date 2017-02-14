<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('columns', $this) ?>
<?php } ?>
<?php $equalHeight = $this->checkbox('equalHeight')->isChecked() && !$this->editmode; ?>
<div class="toolbox-element toolbox-columns <?= $this->select('columnsAdditionalClasses')->getData(); ?><?= $equalHeight ? ' equal-height' : '' ?>">
    <?= $this->template('toolbox/columns.php') ?>
</div>