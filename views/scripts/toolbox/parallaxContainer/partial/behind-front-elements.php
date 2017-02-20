<?php foreach ($this->elements->getElements() as $element) { ?>
    <?php

    $imageUrl = $element['obj'] instanceOf \Pimcore\Model\Asset
        ? $element['obj']->getThumbnail('parallaxBackground')
        : '';

    $backgroundImageTag = $this->backgroundImageMode === 'style'
        ? 'style="background-image:url(' . $imageUrl . ');"'
        : 'data-background-image="' . $imageUrl . '"';; ?>

    <div class="element position-<?= $element['parallaxPosition']; ?>" <?= $backgroundImageTag; ?> data-element-position="<?= $element['parallaxPosition']; ?>"></div>

<?php } ?>