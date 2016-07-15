<?php if ( is_array($this->downloads) && count($this->downloads) > 0 ) { ?>
    <div class="download-list<?=$this->checkbox('showPreviewImages')->isChecked() ? ' show-image-preview' : ''?>">

        <ul class="list-unstyled">

            <?php foreach($this->downloads as $download) { ?>

                <?php if ($download instanceof \Pimcore\Model\Asset) { ?>

                    <?= $this->template('toolbox/download/list/item.php', array('download' => $download, 'showPreviewImages' => $this->checkbox('showPreviewImages')->isChecked(), 'showFileInfo' => $this->checkbox('showFileInfo')->isChecked())) ?>

                <?php } ?>

            <?php } ?>

        </ul>

    </div>
<?php } ?>