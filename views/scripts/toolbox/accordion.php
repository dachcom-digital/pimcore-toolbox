<div class="panel-group" id="<?= $this->id ?>" role="tablist" aria-multiselectable="true">

    <?php while ($this->panel->loop()) { ?>

        <div class="panel <?= $this->type ?>">

            <div
                class="panel-heading collapsed"
                data-toggle="<?= $this->editmode ? '' : 'collapse' ?>"
                data-parent="#<?= $this->id ?>"
                data-target="#panel-<?= $this->id ?>-<?= $this->panel->getCurrentIndex() ?>">
                <h3 class="panel-title">
                    <a class="accordion-toggle" role="button"><?= $this->input('name') ?></a>
                </h3>

            </div>

            <div id="panel-<?= $this->id ?>-<?= $this->panel->getCurrentIndex() ?>" class="panel-collapse collapse <?= $this->editmode ? 'in' : '' ?>">

                <div class="panel-body">
                    <?= $this->template('helper/areablock.php', ['name' => 'accord', 'type' => 'accordion']); ?>
                </div>

            </div>

        </div>

    <?php } ?>

</div>

