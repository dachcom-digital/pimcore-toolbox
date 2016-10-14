<?php if (is_array($this->downloads) && count($this->downloads) > 0) { ?>

    <div class="download-list<?= $this->showPreviewImages ? ' show-image-preview' : '' ?>">

        <ul class="list-unstyled">

            <?php foreach ($this->downloads as $download) { ?>

                <?php if ($download instanceof \Pimcore\Model\Asset) { ?>

                    <?= $this->template('toolbox/download/list/item.php', [
                        'download'          => $download,
                        'showPreviewImages' => $this->showPreviewImages,
                        'showFileInfo'      => $this->showFileInfo
                    ]) ?>

                <?php } ?>

            <?php } ?>

        </ul>

    </div>
    
<?php } ?>