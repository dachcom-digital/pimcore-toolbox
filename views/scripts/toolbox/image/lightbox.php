<a href="<?= $this->image('ci', array('thumbnail' => 'lightBoxImage'))->getThumbnail(); ?>" class="item">
    <?php $this->template('toolbox/image/single.php', array('class' => 'img-responsive ' . $this->class)); ?>
</a>