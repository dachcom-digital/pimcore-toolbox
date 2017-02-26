<div class="parallax-background <?= $this->backgroundColorClass; ?>" <?= $this->backgroundTags ?>>
    <?= $this->partial('toolbox/parallaxContainer/partial/parallax-content.php',
        [
            'behindElements' => $this->behindElements,
            'frontElements'  => $this->frontElements,
            'sectionContent' => $this->sectionContent
        ]
    ); ?>
</div>