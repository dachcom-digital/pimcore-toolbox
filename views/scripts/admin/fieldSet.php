<?php if( !empty( $this->configElements ) ) { ?>

    <div class="toolbox-element-edit-button"></div>
    <div class="toolbox-element-window toolbox-element-window-hidden">

        <div class="toolbox-edit-overlay">

            <?php foreach( $this->configElements as $configElement) { ?>

                <div class="toolbox-element" data-reload="<?= $configElement['edit-reload'] ? 'true' : 'false'; ?>">

                    <div class="t-row">
                        <label><?= $configElement['title']; ?> <?= !in_array(substr($configElement['title'], -1),array('.',',',':','!','?')) ? ':' : '' ?></label>
                        <?= $this->template('admin/elements/' . $configElement['type'] . '.php', array('element' => $configElement)) ?>
                    </div>

                </div>

            <?php } ?>

        </div>

    </div>

<?php } ?>