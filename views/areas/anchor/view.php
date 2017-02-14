<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('anchor', $this) ?>
<?php } ?>

<?php if (!$this->input('anchorName')->isEmpty()) { ?>
    <?php $anchorName = ltrim($this->input('anchorName')->getData(), '#'); ?>
    <a class="toolbox-element toolbox-anchor" id="<?= $anchorName ?>" data-anchortitle="<?= $this->input('anchorTitle')->getData() ?>"></a>
<?php } ?>