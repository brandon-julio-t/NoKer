<?php

class UserRepository
{
  public static function getAll()
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('select * from users order by name');
    $query->execute();
    $result = $query->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $models[] = User::fromStdClass($obj);
    }

    $db->close();
    return $models;
  }

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
    $query = $db->prepare('insert into users (`id`, `name`, `email`, `password`) values (?, ?, ?, ?)');
    $query->bind_param('ssss', $user->id, $user->name, $user->email, $user->password);
    $query->execute();
    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }

  public static function update(User $user)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('
      update users set
        `name` = ?,
        `email` = ?,
        `blocked_at` = ?
      where `id` = ?
    ');
    $query->bind_param('ssss', $user->name, $user->email, $user->blocked_at, $user->id);
    $query->execute();
    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
