<?= $this->template('toolbox/download/list.php', [
    'editmode' => $this->editmode,
    'showPreviewImages' => $this->checkbox('showPreviewImages')->isChecked(),
    'showFileInfo' => $this->checkbox('showFileInfo')->isChecked()
]) ?>