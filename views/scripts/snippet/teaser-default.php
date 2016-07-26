<?php
$lightBoxParam = !is_null($this->getParam('useLightBox')) ? $this->getParam('useLightBox') : $this->checkbox('useLightBox')->isChecked();
$useLightBox = $lightBoxParam && !$this->editmode;
$hasLink = !$this->globallink('link')->isEmpty();
?>
<div class="single-teaser default <?= $useLightBox ? 'light-gallery' : ''; ?>">

    <?= !$this->editmode && ($useLightBox || $hasLink) ? '<a href="' . $this->image('image')->getThumbnail('lightBoxImage') . '">' : (!$this->editmode && $hasLink ? '<a href="' . $this->globallink('link')->getHref() . '" class="item">' : ''); ?>

    <?= $this->image('image', [

        'thumbnail' => 'standardTeaser',
        'class' => 'img-responsive'

    ]) ?>

    <?= !$this->editmode && ($useLightBox || $hasLink) ? '</a>' : ''; ?>

    <h3 class="teaser-headline"><?= $this->input('headline') ?></h3>

    <div class="teaser-text">
        <?= $this->wysiwyg('text', ['height' => 100, 'customConfig' => '/toolbox-ckeditor-style.js']); ?>
    </div>

    <?= $this->globallink('link', ['class' => 'btn btn-default teaser-link']); ?>

</div>