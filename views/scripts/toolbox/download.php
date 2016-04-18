<?php if ($this->editmode) { ?>

    <div class="alert alert-info form-inline">

        <?php if( $this->toolboxHelper()->hasAdditionalClasses('downloads') ) { ?>

            <div class="form-group">
                <label> Zusatz:</label>
            </div>
            <div class="form-group">

                <?php

                $acStore = $this->toolboxHelper()->getConfigArray( 'downloads/additionalClasses', TRUE, TRUE );
                echo $this->select('downloadsAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
                ?>

            </div>
        <?php } ?>

    </div>

<?php } ?>

<div class="toolbox-download <?= $this->select('downloadsAdditionalClasses')->getData();?>">

    <?php if ( count($this->multihref("downloads")) > 0 ) { ?>

        <div class="download-list">
            <ul class="list-unstyled">

            <?php foreach($this->multihref("downloads") as $download) { ?>

                <?php if ($download instanceof \Pimcore\Model\Asset\Document) {

                    $dPath = $download->getFullPath();
                    $dSize = $download->getFileSize('kb', 2);
                    $dType = Pimcore\File::getFileExtension($download->getFilename());
                    $dName = ($download->getMetadata('name')) ? $download->getMetadata('name') : 'Download';

                    ?>

                    <li>
                        <a href="<?= $dPath; ?>" target="_blank" class="icon-<?= $dType; ?>">
                            <?= $dName; ?>
                        </a>
                    </li>

                <?php } ?>

            <?php } ?>

            </ul>
        </div>

    <?php } ?>

</div>