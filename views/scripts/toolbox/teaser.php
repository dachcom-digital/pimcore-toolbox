<div class="toolbox-teaser">

    <div class="row">

        <div class="col-sm-12">

            <?php if($this->editmode) { ?>

                <div class="editmode-label">

                    <div class="alert alert-info form-inline">

                        <div class="form-group">
                            <label>Type:</label>
                        </div>
                        <div class="form-group">

                            <?php

                            if ($this->select("type")->isEmpty()) {

                                $this->select("type")->setDataFromResource("direct");

                            }

                            echo $this->select("type", [

                                "width" => 100,
                                "reload" => true,
                                "store" => [["direct",$this->translate('Direct')], ["snippet",$this->translate('Snippet')]]

                            ]);

                            ?>
                        </div>

                        <div class="form-group">
                            <label class="checkbox">Lightbox?</label>
                        </div>
                        <div class="form-group">

                            <?php

                            echo $this->checkbox("useLightBox");

                            ?>
                        </div>

                    </div>

                </div>

            <?php } ?>

            <?php

            $type = $this->select("type")->getData();

            if($type == "direct") {

                $this->template("/snippets/standard-teaser.php");

            } else {

                echo $this->snippet("teaser");

            }

            ?>
        </div>

    </div>

</div>
