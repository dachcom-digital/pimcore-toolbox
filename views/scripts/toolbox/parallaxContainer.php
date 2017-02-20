<?php $args = [
    'backgroundImageTag' => $this->backgroundImageTag,
    'behindElements'     => $this->behindElements,
    'frontElements'      => $this->frontElements,
    'content'            => $this->content
]; ?>
<?= $this->partial('toolbox/parallaxContainer/partial/background-' . $this->backgroundMode . '.php', $args); ?>