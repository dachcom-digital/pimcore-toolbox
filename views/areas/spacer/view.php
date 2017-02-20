<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('spacer', $this) ?>
<?php }?>
<div class="toolbox-element toolbox-spacer <?= $this->select('spacerAdditionalClasses')->getData();?>" <?= $this->editmode ? 'data-type="' . $this->select('spacerClass')->getData() . '"' : '' ?>>
    <?= $this->template('toolbox/spacer.php', ['spacerClass' => $this->select('spacerClass')->getData()]);?>
</div>