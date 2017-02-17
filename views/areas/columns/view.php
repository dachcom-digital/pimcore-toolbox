<?= $this->adminData; ?>
<div class="toolbox-element toolbox-columns <?= $this->select('columnsAdditionalClasses')->getData(); ?><?= $this->equalHeight ? ' equal-height' : '' ?>">
    <?= $this->template('toolbox/columns.php') ?>
</div>