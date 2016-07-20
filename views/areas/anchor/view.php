<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('anchor', $this) ?>
<?php }?>

<?php if ( !$this->input('anchorName')->isEmpty() ) { ?>
    <a id="<?=$this->input('anchorName')->getData()?>"></a>
<?php } ?>