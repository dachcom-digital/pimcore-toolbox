<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('video', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-video <?= $this->select('videoContainerAdditionalClasses')->getData(); ?><?= $this->autoPlay ? ' autoplay' : '' ?>" data-type="<?= $this->videoType ?>">
    <?= $this->template('toolbox/video.php') ?>
</div>