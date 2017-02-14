<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('video', $this) ?>
<?php } ?>
<?php

    $playInLightbox = $this->vhs('video')->getShowAsLightbox() === TRUE ? 'true' : 'false';
    $autoPlay = $this->checkbox('autoplay')->isChecked() === '1' && !$this->editmode;
    $videoType = $this->vhs('video')->getVideoType();
    $posterPath = NULL;
    $imageThumbnail = NULL;
    $poster = $this->vhs('video')->getPosterAsset();

    if ($poster instanceof \Pimcore\Model\Asset\Image) {
        $configNode = \Toolbox\Config::getConfig()->video->toArray();
        if (isset($configNode['videoOptions'])) {
            if (isset($configNode['videoOptions'][$videoType])) {
                $options = $configNode['videoOptions'][$videoType];
                $imageThumbnail = isset($options['posterImageThumbnail']) ? $options['posterImageThumbnail'] : NULL;
            }
        }

        $posterPath = $poster->getThumbnail($imageThumbnail);
    }

?>

<div class="toolbox-element toolbox-video <?= $this->select('videoContainerAdditionalClasses')->getData(); ?><?= $autoPlay ? ' autoplay' : '' ?>" data-type="<?= $videoType ?>">
    <?= $this->template('toolbox/video.php', ['autoplay' => $autoPlay, 'posterPath' => $posterPath, 'videoType' => $videoType, 'playInLightbox' => $playInLightbox,]) ?>
</div>