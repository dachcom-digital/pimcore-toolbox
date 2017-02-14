<?= $this->parallaximage($this->element['name'],
    [
        'width'    => $this->element['width'],
        'height'   => $this->element['height'],
        'position' => $this->element['position'],
        'types'    => ['asset'],
        'subtypes' => [
            'asset' => ['image']
        ],
        'class'    => $this->element['class'],
    ]
); ?>