<div class="row">

    <div class="col-sm-12">

        <?php

        $type = $this->select('type')->getData();

        if($type == 'direct')
        {
            $this->template('/snippets/teaser-standard.php');
        }
        else
        {
            echo $this->snippet('teaser');
        }

        ?>

    </div>

</div>

