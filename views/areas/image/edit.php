<div class="toolbox-edit-overlay">

    <div class="t-row">
        <label><?= $this->translateAdmin('Extra CSS-Class') ?></label>
        <?= $this->template('helper/default-edit.php'); ?>
    </div>
    <div class="t-row">
        <label><?= $this->translateAdmin('Use Lightbox') ?></label>
        <?= $this->checkbox('useLightbox'); ?>
    </div>
    <div class="t-row">
        <label><?= $this->translateAdmin('Display Caption') ?></label>
        <?= $this->checkbox('showCaption'); ?>
    </div>

</div>