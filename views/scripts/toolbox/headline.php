<?php if ($this->editmode) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Grösse:</label>
        </div>
        <div class="form-group">

            <?php

            $store = $this->toolboxHelper()->getConfigArray( 'headlines/tags', TRUE );

            if ($this->select('headlineType')->isEmpty())
            {
                $this->select('headlineType')->setDataFromResource('h3');
            }

            echo $this->select('headlineType', array('store' => $store, 'width' => 200, 'reload' => true));

            ?>

        </div>

        <?php if( $this->toolboxHelper()->hasAdditionalClasses('headlines') ) { ?>

            <div class="form-group">
            <label> Zusatz:</label>
            </div>
            <div class="form-group">

                <?php

                    $acStore = $this->toolboxHelper()->getConfigArray( 'headlines/additionalClasses', TRUE );
                    echo $this->select('headlineAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
                ?>

            </div>
        <?php } ?>

    </div>

<?php } ?>

<div class="toolbox-headline <?= $this->select('headlineAdditionalClasses')->getData();?>">
    <<?= $this->select('headlineType')->getData();?>><?= $this->input('headlineText'); ?></<?=$this->select('headlineType')->getData();?>>
</div>