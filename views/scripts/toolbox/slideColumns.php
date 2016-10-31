<?php  ?>

<div class="row">
    <div class="slide-columns slide-elements-<?= $this->slidesPerView ?> <?= $this->id ?>" data-slides="<?= $this->slidesPerView ?>">

        <?php $c = 1; ?>
        <?php while ($this->slideElements->loop()) { ?>

            <div class="col <?= $this->slidesPerViewClasses ?>">

                <?php if( $this->editmode) { ?>
                    <span class="count"><?= $c ?> / <?= $this->slideElements->getCount() ?></span>
                <?php } ?>

                <div class="slide-column <?= $type ?> slide-<?= $this->slideElements->getCurrentIndex() ?>">
                    <?= $this->template('helper/areablock.php', [ 'name' => 'slideCol'.  $this->slideElements->getCurrentIndex(), 'type' => 'slideColumns' ] ); ?>
                </div>

            </div>

            <?php $c++; ?>

        <?php } ?>

    </div>
</div>