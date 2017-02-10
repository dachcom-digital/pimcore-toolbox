<?= $this->multihref($this->element['name'],
    [
        'width'      => $this->element['width'],
        'height'     => $this->element['height'],
        'title'      => $this->element['title'],
        'uploadPath' => $this->element['uploadPath'],
        'types'      => $this->element['types'],
        'subtypes'   => $this->element['subtypes'],
        'classes'    => $this->element['classes'],
        'class'      => $this->element['class'],
    ]
); ?>