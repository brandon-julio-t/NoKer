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

  public static function getAllPaginated(int $page = 1)
  {
    $db = MySqlAdapter::get();
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $query = $db->prepare('select * from users order by name limit ? offset ?');
    $query->bind_param('ii', $limit, $offset);
    $query->execute();
    $result = $query->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $models[] = User::fromStdClass($obj);
    }

    $result = $db->query('select count(*) as count from users');
    $totalCount = $result->fetch_object()->count;

    $db->close();
    return [$models, $totalCount];
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

  public static function getAllFollowersByUser(User $user)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('
      select *
      from users u
        join user_friends uf on u.id = uf.friender_id
      where uf.friendee_id = ?
      order by u.name
    ');
    $query->bind_param('s', $user->id);
    $query->execute();
    $result = $query->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $models[] = User::fromStdClass($obj);
    }

    $db->close();
    return $models;
  }

  public static function getAllFollowingsByUser(User $user)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('
      select *
      from users u
        join user_friends uf on u.id = uf.friendee_id
      where uf.friender_id = ?
      order by u.name
    ');
    $query->bind_param('s', $user->id);
    $query->execute();
    $result = $query->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $models[] = User::fromStdClass($obj);
    }

    $db->close();
    return $models;
  }

  public static function create(User $user)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('
      insert into users (
        `id`,
        `name`,
        `email`,
        `password`,
        `profile_picture`,
        `balance`,
        `is_premium`
      )
      values (?, ?, ?, ?, ?, ?, ?)
    ');
    $query->bind_param(
      'sssssii',
      $user->id,
      $user->name,
      $user->email,
      $user->password,
      $user->profile_picture,
      $user->balance, $user->is_premium
    );
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
        `profile_picture` = ?,
        `balance` = ?,
        `is_premium` = ?,
        `blocked_at` = ?
      where `id` = ?
    ');
    $query->bind_param(
      'sssssss',
      $user->name,
      $user->email,
      $user->profile_picture,
      $user->balance,
      $user->is_premium,
      $user->blocked_at,
      $user->id
    );
    $query->execute();
    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
