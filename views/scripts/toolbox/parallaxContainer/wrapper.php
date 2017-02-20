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

    $areaBlock = $this->template('helper/areablock.php', ['name' => 'p-container-block', 'type' => 'parallaxContainer'], FALSE, TRUE);

    if ($this->editmode) {
        if ($this->containerWrapper === 'none' && strpos($areaBlock, 'toolbox-columns') !== FALSE) {
            $warning = '<div class="alert alert-danger">' . $this->translateAdmin('You\'re using columns without a valid container wrapper.') . '</div>' . "\n";
            $areaBlock = $warning . $areaBlock;
        }
    }

    $wrapContent = '%s';
    if ($this->containerWrapper !== 'none') {
        $wrapContent = '<div class="toolbox-container"><div class="' . $this->containerWrapper . '">%s</div></div>';
    }

    $content = sprintf($wrapContent, $areaBlock);

    $args = [
        'backgroundImageTag' => $backgroundImageTag,
        'behindElements'     => $behindElements,
        'frontElements'      => $frontElements,
        'content'            => $content
    ];

?>
<?= $this->partial('toolbox/parallaxContainer/partial/background-' . $this->bgMode . '.php', $args); ?>