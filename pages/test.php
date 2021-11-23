<?php

$id = '6de529cc-0fef-4993-8e31-bdb5fdd857ac';
$pw = Hash::make('iPhone7Plus');

$db = MySqlAdapter::get();
$q = $db->prepare('update users set password = ? where id = ?');
$q->bind_param('ss', $pw, $id);
$q->execute();
$db->close();
echo $q->affected_rows;
