<?php if (!empty ($this->configElements)) { ?>

    <div class="toolbox-element-edit-button"></div>
    <div class="toolbox-element-window toolbox-element-window-hidden" data-edit-window-size="<?= $this->windowSize; ?>"><?php

        $content = '';
        $content .= '<div class="toolbox-edit-overlay ' . $this->windowSize . '">';
            $content .= '<div class="t-row clearfix" data-index="0">';

            $halfCounter = 0;
            $rowCounter = 0;

            $lastColClass = NULL;

            foreach ($this->configElements as $c => $configElement) {

                if ($configElement['col-class'] === 't-col-half') {
                    $halfCounter++;
                } else {
                    $halfCounter = 0;
                }

                if( $configElement['col-class'] === 't-col-full' && $lastColClass === 't-col-half' ) {
                    $content .= '</div><div class="t-row clearfix" data-index="' . $rowCounter . '">';
                }

                $content .= '<div class="toolbox-element" data-reload="' . ($configElement['edit-reload'] ? 'true' : 'false') . '">';
                    $content .= '<div class="' . $configElement['col-class'] . '">';

                    $content .= '<label>' . $configElement['title'] . ( !in_array(substr($configElement['title'], -1), ['.', ',', ':', '!', '?']) ? ':' : '' ) . '</label>';
                    $captureKey = $configElement['name'] . '_capture';
                    $content .= $this->template('admin/elements/' . $configElement['type'] . '.php', ['element' => $configElement], FALSE, $captureKey);

                    if (isset($configElement['description']) && !empty ($configElement['description'])) {
                        $content .= '<div class="description">' . $configElement['description'] . '</div>';
                    }

                    $content .= '</div><!-- .col-class -->';
                $content .= '</div><!-- .toolbox-element -->';

                if ($halfCounter === 0 || $halfCounter === 2) {
                    $content .= '</div><div class="t-row clearfix" data-index="' . $rowCounter . '">';
                }

                $lastColClass = $configElement['col-class'];
            }

            $content .= '</div><!-- .t-row -->';
        $content .= '</div><!-- .toolbox-edit-overlay -->';

        ?>

        <?php echo $content; ?>

    </div><!-- .toolbox-element-window -->

<?php } ?>