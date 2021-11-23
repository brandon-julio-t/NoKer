<?php

$id = '72dda89a-d1fb-4fcd-ba1b-ede0f2a4b037';
$pw = Hash::make('iPhone7Plus');

$db = MySqlAdapter::get();
$q = $db->prepare('update users set password = ? where id = ?');
$q->bind_param('ss', $pw, $id);
$q->execute();
$q->close();
echo $q->affected_rows;
