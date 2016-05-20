<div class="toolbox-edit-overlay">

    <div class="t-row">
        <label><?= $this->translateAdmin('Images or Folder') ?></label>
        <?= $this->multihref('images', array('width' => '524px')) ?>
    </div>
    <div class="t-row">
        <label><?= $this->translateAdmin('Use Lightbox') ?></label>
        <?= $this->checkbox('useLightbox') ?>
    </div>
    <div class="t-row">
        <label><?= $this->translateAdmin('Use Thumbnails') ?></label>
        <?= $this->checkbox('useThumbnails') ?>
    </div>

</div>