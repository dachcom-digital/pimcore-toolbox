<?= $this->adminData; ?>
<div class="toolbox-element toolbox-slide-columns <?= $this->select('columnsAdditionalClasses')->getData(); ?><?= $this->equalHeight ? ' equal-height' : '' ?>">
    <?= $this->template('toolbox/slideColumns.php'); ?>
</div>