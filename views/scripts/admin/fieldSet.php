<?php if (!empty ($this->configElements)) { ?>

    <div class="toolbox-element-edit-button" data-title="<?= $this->elementTitle; ?>"></div>
    <div class="toolbox-element-window toolbox-element-window-hidden" data-edit-window-size="<?= $this->windowSize; ?>"><?php

        $content = '';
        $content .= '<div class="toolbox-edit-overlay ' . $this->windowSize . '">';


            $halfCounter = 0;
            $rowCounter = 0;

            $lastColClass = NULL;

            foreach ($this->configElements as $c => $configElement) {

                $editModeHiddenClass = $configElement['editmode-hidden'] ? 'editmode-hidden' : '';

                if ( $c === 0 ) {
                    $content .= '<div class="t-row clearfix ' . $editModeHiddenClass . '" data-index="0">';
                }

                if ($configElement['col-class'] === 't-col-half') {
                    $halfCounter++;
                } else {
                    $halfCounter = 0;
                }

                if( $configElement['col-class'] === 't-col-full' && $lastColClass === 't-col-half' ) {
                    $content .= '</div><div class="t-row clearfix ' . $editModeHiddenClass . '" data-index="' . $rowCounter . '">';
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
                    $editModeHiddenClass = isset($this->configElements[$c+1]) && $this->configElements[$c+1]['editmode-hidden'] ? 'editmode-hidden' : '';
                    $content .= '</div><div class="t-row clearfix ' . $editModeHiddenClass . '" data-index="' . $rowCounter . '">';
                }

                $lastColClass = $configElement['col-class'];

                if ( $c === count($this->configElements)-1 ) {
                    $content .= '</div>';
                }
            }

        $content .= '</div><!-- .toolbox-edit-overlay -->';

        ?>

        <?php echo $content; ?>

    </div><!-- .toolbox-element-window -->

<?php } ?>