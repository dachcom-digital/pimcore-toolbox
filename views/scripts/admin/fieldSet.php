<?php if( !empty( $this->configElements ) ) { ?>

    <div class="alert alert-info form-inline">

        <?php foreach( $this->configElements as $configElement) { ?>

            <div class="form-group">
                <label><?= $configElement['title']; ?> <?= !in_array(substr($configElement['title'], -1),array('.',',',':','!','?')) ? ':' : '' ?></label>
            </div>

            <div class="form-group">
                <?= $this->template('admin/elements/' . $configElement['type'] . '.php', array('element' => $configElement)) ?>
            </div>

        <?php } ?>

    </div>

<?php } ?>