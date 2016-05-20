<?php
$useLightBox = $this->checkbox('useLightBox')->isChecked() && !$this->editmode;
$hasLink = !$this->editmode && !$this->globallink('link')->isEmpty();
?>

<div class="single-teaser <?= $useLightBox ? 'light-gallery' : ''; ?>">

    <?= $useLightBox ? '<a href="' . $this->image('image', array('thumbnail' => 'lightBoxImage'))->getThumbnail() . '" class="item">' : ($hasLink ? '<a href="' . $this->globallink('link')->getHref() . '" class="item">' : ''); ?>

    <?= $this->image('image', [

        'thumbnail' => 'standardTeaser',
        'class' => 'img-responsive'

    ]) ?>

    <?= $useLightBox || $hasLink ? '</a>' : ''; ?>

    <?php if($this->editmode) { ?>


    <?php } ?>

    <h3><?= $this->input('headline') ?></h3>

    <div>
        <?= $this->wysiwyg('text', ['height' => 100]); ?>
    </div>

    <p>
        <?= $this->globallink('link', ['class' => 'btn btn-default']); ?>
    </p>

    <?php
    // unset the suffix otherwise it will cause problems when using in a loop
    $this->suffix = null;

    ?>

</div>