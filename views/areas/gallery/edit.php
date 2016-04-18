<div class="toolbox-edit-overlay">

    <div class="t-row">
        <label><?= $this->translate("Images or Folder") ?></label>
        <?= $this->multihref("images", array('width' => '524px')) ?>
    </div>
    <div class="t-row">
        <label><?= $this->translate("Use Thumbnails") ?></label>
        <?= $this->checkbox("useThumbnails") ?>
    </div>

</div>