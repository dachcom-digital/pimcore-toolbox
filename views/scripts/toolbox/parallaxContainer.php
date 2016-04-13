<?php



?>
<div class="toolbox-parallax-container <?= $this->select('parallaxContainerAdditionalClasses')->getData();?>">

    <?php if($this->editmode) { ?>

        <div class="editmode-label">

            <div class="alert alert-info form-inline">

                <div class="form-group">
                    <label>Type:</label>
                </div>
                <div class="form-group">

                    <?php

                    if ($this->select("type")->isEmpty()) {
                        $this->select("type")->setDataFromResource("image");
                    }

                    echo $this->select("type", [

                        "width" => 100,
                        "reload" => true,
                        "store" => [["image",$this->translate('Image')], ["snippet",$this->translate('Snippet')]]

                    ]);

                    ?>
                </div>

                <?php if( $this->toolboxHelper()->hasAdditionalClasses('parallaxContainer') ) { ?>
                    <div class="form-group">
                        <label>Zusatz:</label>
                    </div>
                    <div class="form-group">

                        <?php

                        $acStore = $this->toolboxHelper()->getConfigArray( 'parallaxContainer/additionalClasses', TRUE, TRUE );
                        echo $this->select('parallaxContainerAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
                        ?>

                    </div>
                <?php } ?>

            </div>

        </div>

    <?php } ?>

    <?php

    $type = $this->select("type")->getData();

    if($type == "image") {

        $this->template("/toolbox/parallaxContainer/image.php");

    } else {

        $this->template("/toolbox/parallaxContainer/snippet.php");

    }

    ?>
</div>