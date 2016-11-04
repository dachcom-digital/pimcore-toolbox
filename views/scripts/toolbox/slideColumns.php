<div class="row">
    <div class="slide-columns slide-elements-<?= $this->slidesPerView ?> <?= $this->id ?>" data-slides="<?= $this->slidesPerView ?>" data-breakpoints="<?=htmlentities(json_encode($this->breakpoints))?>">

        <?php $c = 1; ?>
        <?php while ($this->slideElements->loop()) { ?>

            <div class="col <?= $this->slidesPerViewClasses ?>">

                <?php if( $this->editmode) { ?>
                    <span class="count"><?= $c ?> / <?= $this->slideElements->getCount() ?></span>
                <?php } ?>

                <div class="slide-column slide-<?= $this->slideElements->getCurrentIndex() ?>">
                    <?= $this->template('helper/areablock.php', [ 'name' => 'slideCol', 'type' => 'slideColumns' ] ); ?>
                </div>

            </div>

            <?php $c++; ?>

        <?php } ?>

    </div>
</div>