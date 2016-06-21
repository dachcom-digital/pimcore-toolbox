<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('video', $this) ?>
<?php }?>
<?php
$autoplay = $this->checkbox('autoplay')->isChecked() === '1' && !$this->editmode;
?>
<div class="toolbox-video <?= $this->select('videoContainerAdditionalClasses')->getData();?><?= $autoplay ? ' autoplay' : ''?>" data-type="<?php echo $this->video('video')->getVideoType() ?>">
    <?= $this->template('toolbox/video.php', ['autoplay' => $autoplay]) ?>
</div>