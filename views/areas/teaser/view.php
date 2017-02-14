<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('teaser', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-teaser <?= $this->select('teaserAdditionalClasses')->getData(); ?>">
    <?= $this->template('toolbox/teaser.php') ?>
</div>