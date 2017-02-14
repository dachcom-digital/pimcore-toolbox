<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('linklist', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-linklist <?= $this->select('linklistAdditionalClasses')->getData(); ?>">
    <?= $this->template('toolbox/linklist.php') ?>
</div>