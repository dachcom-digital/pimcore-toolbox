<?php $more_content = $this->wysiwyg("page_content_text-" . $this->name, array("width" => "100%", "customConfig" => "/plugins/Toolbox/static/js/wysiwyg-style.js")) ?>

<?php if ($this->editmode && $this->toolboxHelper()->hasAdditionalClasses('content')) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Zusatz:</label>
        </div>
        <div class="form-group">

            <?php

            $acStore = $this->toolboxHelper()->getConfigArray( 'content/additionalClasses', TRUE, TRUE );
            echo $this->select('contentAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
            ?>

        </div>

    </div>

<?php } ?>
<div class="container toolbox-content <?= $this->select('contentAdditionalClasses')->getData();?>">
    <div class="wysiwyg">
        <?= $more_content ?>
    </div>
</div>
