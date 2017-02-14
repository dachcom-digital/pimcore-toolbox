<?= $this->href($this->element['name'],
    [
        'width'      => $this->element['width'],
        'uploadPath' => $this->element['uploadPath'],
        'types'      => $this->element['types'],
        'subtypes'   => $this->element['subtypes'],
        'classes'    => $this->element['classes'],
        'class'      => $this->element['class'],
    ]
); ?>