<?= $this->numeric($this->element['name'],
    [
        'width'            => $this->element['width'],
        'minValue'         => $this->element['minValue'],
        'maxValue'         => $this->element['maxValue'],
        'decimalPrecision' => $this->element['decimalPrecision'],
        'class'            => $this->element['class'],
        'reload'           => $this->element['reload'],
    ]
); ?>