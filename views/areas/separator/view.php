<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('separator', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-separator <?= $this->select('separatorContainerAdditionalClasses')->getData(); ?>">
    <?= $this->template('toolbox/separator.php') ?>
</div>