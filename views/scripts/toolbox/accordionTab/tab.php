<?php $tabContent = []; ?>

<div class="tabs" id="<?= $this->id ?>">

    <ul class="nav nav-tabs list-unstyled" role="tablist">
        <?php while ($this->panel->loop()) { ?>
            <li role="presentation" class="<?= $this->type ?> <?= $this->panel->getCurrentIndex() === '1' ? 'active' : ''?>">
                <?php if($this->editmode) { ?>
                    <?= $this->input('name'); ?>
                    <div class="tab-content">
                        <?php $this->template('helper/areablock.php', ['name' => 'accord', 'type' => 'accordion']); ?>
                    </div>
                <?php } else { ?>
                    <a href="#tab-<?= $this->id ?>-<?= $this->panel->getCurrentIndex() ?>" aria-controls="home" role="tab" data-toggle="tab">
                        <?= $this->input('name')->getData() ?>
                    </a>
                    <?php
                        $tabContent[] = [
                            'index' => $this->panel->getCurrentIndex(),
                            'content' => $this->template('helper/areablock.php', ['name' => 'accord', 'type' => 'accordion'], FALSE, 'toolbox_capture_tab_template')
                        ];
                    ?>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    <?php if(!$this->editmode) { ?>
        <div class="tab-content">
            <?php foreach($tabContent as $content) { ?>
                <div role="tabpanel" class="tab-pane fade <?= $content['index'] === '1' ? 'active in' : ''?>" id="tab-<?= $this->id ?>-<?= $content['index'] ?>">
                    <?= $content['content'] ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>