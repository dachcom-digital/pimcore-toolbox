<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('slideColumns', $this) ?>
<?php } ?>
<?php

    $equalHeight = $this->checkbox('equalHeight')->isChecked() && !$this->editmode;
    $id = $this->brick->getId() . '-' . $this->brick->getIndex();
    $slidesPerView = (int)$this->select('slidesPerView')->getData();
    $slideElements = $this->block('slideCols', ['default' => $slidesPerView]);
    $slidesPerViewClass = $this->toolboxHelper()->calculateSlideColumnClasses($slidesPerView);
    $breakpoints = $this->toolboxHelper()->calculateSlideColumnBreakpoints($slidesPerView);

?>
<div class="toolbox-element toolbox-slide-columns <?= $this->select('columnsAdditionalClasses')->getData(); ?><?= $equalHeight ? ' equal-height' : '' ?>">
    <?= $this->template('toolbox/slideColumns.php',
        [
            'id'                   => $id,
            'slideElements'        => $slideElements,
            'slidesPerView'        => $slidesPerView,
            'slidesPerViewClasses' => $slidesPerViewClass,
            'breakpoints'          => $breakpoints
        ]
    ); ?>
</div>