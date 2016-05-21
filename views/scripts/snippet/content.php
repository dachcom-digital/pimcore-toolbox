<?php if ($this->editmode) { ?>
    <div class="container">
    <div class="row">
    <div class="col-xs-12">
<?php } ?>

<?php $params = $this->toolboxHelper()->getAvailableSnippetBricks(); ?>
<?= $this->areablock(
    'content',
    array(
        'allowed' => $params['allowed'],
        'params' => $params['additional']
    )
); ?>

<?php if ($this->editmode) { ?>
    </div></div></div>
<?php } ?>