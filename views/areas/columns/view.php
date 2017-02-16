<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('columns', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-columns <?= $this->select('columnsAdditionalClasses')->getData(); ?><?= $this->equalHeight ? ' equal-height' : '' ?>">
    <?= $this->template('toolbox/columns.php') ?>
</div>