<ul>
    <?php while ($this->block('linklistblock')->loop()) { ?>
        <li><?= $this->globallink('linklist', ['class' => 'list-link']); ?></li>
    <?php } ?>
</ul>