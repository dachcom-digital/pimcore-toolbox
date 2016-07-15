<div class="toolbox-edit-overlay">

    <div class="t-row">
        <label><?= $this->translateAdmin('Files') ?></label>
        <?= $this->multihref('downloads'); ?>
    </div>

    <div class="t-row">
        <label><?= $this->translateAdmin('Show preview images') ?></label>
        <?= $this->checkbox('showPreviewImages') ?>
    </div>

    <div class="t-row">
        <label><?= $this->translateAdmin('Show file info') ?></label>
        <?= $this->checkbox('showFileInfo') ?>
    </div>

</div>