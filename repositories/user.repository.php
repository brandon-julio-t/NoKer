<?php

class UserRepository
{
  public static function getOneById(string $id)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('select * from users where id = ?');
    $query->bind_param('s', $id);
    $query->execute();
    $model = User::fromStdClass($query->get_result()->fetch_object());
    $db->close();
    return $model;
  }

  public static function getOneByEmail(string $email)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('select * from users where email = ?');
    $query->bind_param('s', $email);
    $query->execute();
    $model = User::fromStdClass($query->get_result()->fetch_object());
    $db->close();
    return $model;
  }

  public static function create(User $user)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('insert into users (id, name, email, password) values (?, ?, ?, ?)');
    $query->bind_param('ssss', useUuid(), $user->name, $user->email, $user->password);
    $query->execute();
    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
