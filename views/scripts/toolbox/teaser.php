<div class="row">

    <div class="col-sm-12">

        <?php

        $type = $this->select('type')->getData();

        if($type == 'direct')
        {
            $layout = $this->select('layout')->getData();

            if( empty( $layout ) )
            {
                $layout = 'standard';
            }

            $this->template('/snippet/teaser-' . $layout . '.php');
        }
        else
        {
            // "teaser-standard" is the snippet name. define your teaser layout in the specific teaser snippet!
            echo $this->snippet('teaser-standard', ['useLightBox' => $this->checkbox('useLightBox')->isChecked()]);
        }

        ?>

    </div>

</div>