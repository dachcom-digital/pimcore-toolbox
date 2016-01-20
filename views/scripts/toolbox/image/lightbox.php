<a href="<?= $this->image("ci", array("thumbnail" => "contentImage"))->getSrc(); ?>" class="item">
    <?php $this->template('image/single.php', array('class' => 'img-responsive ' . $this->class)); ?>
</a>