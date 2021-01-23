
<?php echo $this->tag->linkTo('signup','登录');?>
<?php echo $this->tag->form("signup/register"); ?>

<p>
    <label for="name">Name</label>
    <?php echo $this->tag->textField("name"); ?>
</p>
<p>
    <label for="email">E-Mail</label>
    <?php echo $this->tag->textField("email"); ?>
</p>
<p><?php echo $this->tag->submitButton("注册"); ?></p>

