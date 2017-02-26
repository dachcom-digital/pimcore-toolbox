<?php $args = [
    'backgroundTags'       => $this->backgroundTags,
    'backgroundColorClass' => $this->backgroundColorClass,
    'behindElements'       => $this->behindElements,
    'frontElements'        => $this->frontElements,
    'sectionContent'       => $this->sectionContent
]; ?>

<?= $this->partial('toolbox/parallaxContainer/partial/background-' . $this->backgroundMode . '.php', $args); ?>