<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('download', $this) ?>
<?php }?>
<div class="toolbox-download <?= $this->select('downloadAdditionalClasses')->getData();?>">
    <?= $this->template("toolbox/download.php") ?>
</div>