<?php

$class = 'img-responsive ' . $this->input("extraClass")->text;
$useLightBox = $this->checkbox("useLightbox")->isChecked() && !$this->editmode;

?>
<?php if ($this->editmode && $this->toolboxHelper()->hasAdditionalClasses('image')) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Zusatz:</label>
        </div>
        <div class="form-group">

            <?php

            $acStore = $this->toolboxHelper()->getConfigArray( 'image/additionalClasses', TRUE );
            echo $this->select('imageAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
            ?>

        </div>

    </div>

<?php } ?>

<div class="row">

    <div class="col-xs-12<?php echo $useLightBox ? ' light-gallery' : ''; ?>">

        <div class="toolbox-image <?= $this->select('imageAdditionalClasses')->getData();?>">

            <?php if( $useLightBox ) { ?>

                <?php echo $this->template('toolbox/image/lightbox.php', array( 'class' => $class ) ); ?>

            <?php } else { ?>

                <?php echo $this->template('toolbox/image/single.php', array( 'class' => $class ) ); ?>

            <?php } ?>

        </div>

    </div>

</div>