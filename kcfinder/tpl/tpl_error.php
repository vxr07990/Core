<root>
<?php if (is_array($message)): ?>
<?php   foreach ($message as $msg): ?>
<error><?php echo text::xmlData($msg) ?></error>
<?php   endforeach ?>
<?PHP else: ?>
<error><?php echo text::xmlData($message) ?></error>
<?PHP endif ?>
</root>
