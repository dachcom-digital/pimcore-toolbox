<?php if ($this->select($this->element['name'])->isEmpty()) {
    $this->select($this->element['name'])->setDataFromResource($this->element['default']);
} ?>
<?= $this->select($this->element['name'], ['reload' => $this->element['reload'], 'store' => $this->element['store']]); ?>