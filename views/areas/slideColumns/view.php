<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('slideColumns', $this) ?>
<?php }?>
<?php
$equalHeight = $this->checkbox('equalHeight')->isChecked() && !$this->editmode;
$id = $this->brick->getId() . '-' . $this->brick->getIndex();
$slidesPerView = (int) $this->select('slidesPerView')->getData();
$slideElements = $this->block('slideCols', array('default' => $slidesPerView ));
$slidesPerViewClass = $this->toolboxHelper()->calculateSlideColumnClasses( $slidesPerView );
?>
<div class="toolbox-slide-columns <?= $this->select('columnsAdditionalClasses')->getData();?><?= $equalHeight ? ' equal-height' : ''?>">
    <?= $this->template('toolbox/slideColumns.php', array('id' => $id, 'slideElements' => $slideElements, 'slidesPerView' => $slidesPerView, 'slidesPerViewClasses' => $slidesPerViewClass)) ?>
</div>