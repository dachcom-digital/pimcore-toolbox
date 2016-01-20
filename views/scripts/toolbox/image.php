<?php

$class = 'img-responsive ' . $this->input("extraClass")->text;
$useLightBox = $this->checkbox("useLightbox")->isChecked() && !$this->editmode;

?>
<div class="row">

    <div class="col-xs-12<?php echo $useLightBox ? ' light-gallery' : ''; ?>">

        <div class="toolbox-image">

            <?php if( $useLightBox ) { ?>

                <?php echo $this->template('toolbox/image/lightbox.php', array( 'class' => $class ) ); ?>

            <?php } else { ?>

                <?php echo $this->template('toolbox/image/single.php', array( 'class' => $class ) ); ?>

            <?php } ?>

        </div>

    </div>

</div>