<?php if ( count($this->multihref('downloads')) > 0 ) { ?>

    <div class="download-list">

        <ul class="list-unstyled">

        <?php foreach($this->multihref('downloads') as $download) { ?>

            <?php if ($download instanceof \Pimcore\Model\Asset\Document) {

                $dPath = $download->getFullPath();
                $dSize = $download->getFileSize('kb', 2);
                $dType = Pimcore\File::getFileExtension($download->getFilename());
                $dName = ($download->getMetadata('name')) ? $download->getMetadata('name') : 'Download';

                ?>

                <li>
                    <a href="<?= $dPath; ?>" target="_blank" class="icon-download-<?= $dType; ?>">
                        <?= $dName; ?>
                    </a>
                </li>

            <?php } ?>

        <?php } ?>

        </ul>

    </div>

<?php } ?>