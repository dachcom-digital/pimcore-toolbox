<div class="toolbox-edit-overlay">

    <div class="t-row">
        <label><?= $this->translate("Extra CSS-Class") ?></label>
        <?= $this->template("helper/default-edit.php"); ?>
    </div>
    <div class="t-row">
        <label><?= $this->translate("Use Lightbox") ?></label>
        <?= $this->checkbox("useLightbox"); ?>
    </div>
    <div class="t-row">
        <label><?= $this->translate("Display Caption") ?></label>
        <?= $this->checkbox("showCaption"); ?>
    </div>

</div>