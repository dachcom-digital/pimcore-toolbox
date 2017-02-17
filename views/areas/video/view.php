<?= $this->adminData; ?>
<div class="toolbox-element toolbox-video <?= $this->select('videoContainerAdditionalClasses')->getData(); ?><?= $this->autoPlay ? ' autoplay' : '' ?>" data-type="<?= $this->videoType ?>">
    <?= $this->template('toolbox/video.php') ?>
</div>