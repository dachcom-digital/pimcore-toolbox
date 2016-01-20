<div>

    <label><?= $this->translate("Images or Folder") ?></label>
    <?= $this->multihref("images", array('width' => '524px')) ?>
    <br>
    <label><?= $this->translate("Use Thumbnails") ?></label>
    <?= $this->checkbox("useThumbnails") ?>

</div>