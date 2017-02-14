<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('content', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-content wysiwyg content-container <?= $this->select('contentAdditionalClasses')->getData(); ?>">
    <?= $this->template('toolbox/content.php') ?>
</div>