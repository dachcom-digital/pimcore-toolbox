<?= $this->parallaximage($this->element['name'],
    [
        'width'    => $this->element['width'],
        'height'   => $this->element['height'],
        'position' => $this->element['position'],
        'size'     => $this->element['size'],
        'types'    => ['asset'],
        'subtypes' => [
            'asset' => ['image', 'video']
        ],
        'class'    => $this->element['class'],
    ]
); ?>