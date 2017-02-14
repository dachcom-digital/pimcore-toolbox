<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('container', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-container <?= $this->select('containerAdditionalClasses')->getData(); ?>">
    <?= $this->template('toolbox/container.php') ?>
</div>