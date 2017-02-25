<?php if (!empty($this->behindElements)) { ?>
    <div class="behind-elements"><?= $this->behindElements; ?></div>
<?php } ?>

<div class="parallax-content"><?= $this->sectionContent; ?></div>

<?php if (!empty($this->frontElements)) { ?>
    <div class="front-elements"><?= $this->frontElements; ?></div>
<?php } ?>