<h3 class="panel-title">
    <?php if($this->editmode) { ?>
        <?= $this->input('name') ?>
    <?php } else { ?>
        <a class="accordion-toggle" role="button"><?= $this->input('name')->getData() ?></a>
    <?php } ?>
</h3>