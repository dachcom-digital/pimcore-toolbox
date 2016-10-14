<?= $this->partials('toolbox/download/list.php', [
    'editmode' => $this->editmode,
    'downloads' => $this->multihref('downloads')->getElements(),
    'showPreviewImages' => $this->checkbox('showPreviewImages')->isChecked(),
    'showFileInfo' => $this->checkbox('showFileInfo')->isChecked()
]) ?>