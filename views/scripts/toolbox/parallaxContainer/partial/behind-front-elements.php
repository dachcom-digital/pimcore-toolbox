<?php foreach ($this->elements->getElements() as $element) { ?>
    <?php

    $imageUrl = $element['obj'] instanceOf \Pimcore\Model\Asset
        ? $element['obj']->getThumbnail('parallaxImage')
        : '';

    $parallaxImageTag = $this->backgroundImageMode === 'style'
        ? 'style="background-image:url(' . $imageUrl . ');"'
        : 'data-background-image="' . $imageUrl . '"';

    $orgWidth = $element['obj']->getWidth();
    $width = $element['obj']->getThumbnail('parallaxImage')->getWidth();
    $orgHeight = $element['obj']->getHeight();
    $height = $element['obj']->getThumbnail('parallaxImage')->getHeight();

    ?>
    <div class="element position-<?= $element['parallaxPosition']; ?> size-<?= $element['parallaxSize']; ?>"
        <?= $parallaxImageTag; ?>
        data-width="<?= empty($width) ? $orgWidth : $width; ?>" data-height="<?= empty($height) ? $orgHeight : $height; ?>"
        data-element-position="<?= $element['parallaxPosition']; ?>"
        data-element-size="<?= $element['parallaxSize']; ?>">
    </div>
<?php } ?>