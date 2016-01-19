<?php

if ($this->editmode) {

    echo '<br/><br/><br/>';

}

$class = $this->input("extraClass")->text;
$useLightBox = $this->checkbox("useLightbox")->isChecked() && !$this->editmode;

?>

<div class="row">

    <div class="col-xs-12<?php echo $useLightBox ? ' light-gallery' : ''; ?>">

        <?php if( $useLightBox ) { ?>
            <a href="<?= $this->image("ci", array("thumbnail" => "contentImage"))->getSrc(); ?>" class="item">
        <?php } ?>

            <?= $this->image("ci", array("thumbnail" => "contentImage", "class" => "img-responsive " . $class, "height" => 200)); ?>

        <?php if( $useLightBox ) { ?>
            </a>
        <?php } ?>

    </div>

</div>
