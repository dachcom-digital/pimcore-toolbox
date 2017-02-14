<?php

    $imageUrl = $this->parallaxBackground->getElement() instanceOf \Pimcore\Model\Asset
        ? $this->parallaxBackground->getElement()->getThumbnail('parallaxBackground')
        : '';

    $backgroundImageTag = $this->backgroundImageMode === 'style'
        ? 'style="background-image:url(' . $imageUrl . ');"'
        : 'data-background-image="' . $imageUrl . '"';

    $behindElements = !empty($this->parallaxBehind)
        ? $this->partial('toolbox/parallaxContainer/partial/behind-front-elements.php', ['elements' => $this->parallaxBehind, 'backgroundImageMode' => $this->backgroundImageMode])
        : NULL;
    $frontElements = !empty($this->parallaxFront)
        ? $this->partial('toolbox/parallaxContainer/partial/behind-front-elements.php', ['elements' => $this->parallaxFront, 'backgroundImageMode' => $this->backgroundImageMode])
        : NULL;

    $content  = $this->template('helper/areablock.php', ['name' => 'p-container-block', 'type' => 'parallaxContainer'], FALSE, TRUE);

    $args = [
        'backgroundImageTag'    => $backgroundImageTag,
        'behindElements'        => $behindElements,
        'frontElements'         => $frontElements,
        'content'               => $content
    ];

?>
<?= $this->partial('toolbox/parallaxContainer/partial/background-' . $this->bgMode . '.php', $args); ?>