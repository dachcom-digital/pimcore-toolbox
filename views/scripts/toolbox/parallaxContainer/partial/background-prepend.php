<div class="parallax-background <?= $this->backgroundColorClass; ?>" <?= $this->backgroundTags ?>></div>
<div class="parallax-content-wrapper">

    <?= $this->partial('toolbox/parallaxContainer/partial/parallax-content.php',
        [
            'behindElements' => $this->behindElements,
            'frontElements'  => $this->frontElements,
            'sectionContent' => $this->sectionContent
        ]
    ); ?>

</div>
