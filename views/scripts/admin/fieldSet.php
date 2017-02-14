<?php if (!empty ($this->configElements)) { ?>

    <div class="toolbox-element-edit-button"></div>
    <div class="toolbox-element-window toolbox-element-window-hidden" data-edit-window-size="<?= $this->windowSize; ?>">

        <div class="toolbox-edit-overlay <?= $this->windowSize; ?>">

            <div class="t-row clearfix" data-index="0">

                <?php foreach ($this->configElements as $c => $configElement) { ?>

                    <?= $c > 0 && $c % 2 === 0 ? '</div><div class="t-row clearfix" data-index="' . $c . '">' : ''; ?>

                    <div class="toolbox-element" data-reload="<?= $configElement['edit-reload'] ? 'true' : 'false'; ?>">

                        <div class="<?= $configElement['col-class']; ?>">

                            <label><?= $configElement['title']; ?> <?= !in_array(substr($configElement['title'], -1), ['.', ',', ':', '!', '?']) ? ':' : '' ?></label>
                            <?= $this->template('admin/elements/' . $configElement['type'] . '.php', ['element' => $configElement]) ?>
                            <?php if (isset($configElement['description']) && !empty ($configElement['description'])) { ?>
                                <div class="description">
                                    <?= $configElement['description']; ?>
                                </div>
                            <?php } ?>

                        </div>

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>

<?php } ?>