<?php $fileInfo = $this->toolboxHelper()->getDownloadInfo($this->download, 'kb', $this->showPreviewImages, $this->showFileInfo); ?>

<li>
    <a href="<?= $fileInfo['path']; ?>" <?= $this->toolboxHelper()->addTracker('download', $this->download); ?> target="_blank" class="icon-download-<?= $fileInfo['type']; ?>">
        <?php if ($this->showPreviewImages) { ?><span class="preview-image"><img src="<?= ['previewImage'] ?>" alt="<?= $fileInfo['altText'] ?>"/></span><?php } ?>
        <span class="title"><?= $fileInfo['name']; ?></span>
        <?php if ($this->showFileInfo) { ?> <span class="file-info">(<span class="file-type"><?= $fileInfo['type'] ?></span>, <?= $fileInfo['size'] ?>)</span><?php } ?>
    </a>
</li>