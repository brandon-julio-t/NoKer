<?php

class UserFriendRepository
{
  public static function create(UserFriend $userFriend)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('
      insert into user_friends (friender_id, friendee_id)
      values (?, ?)
    ');
    $query->bind_param(
      'ss',
      $userFriend->friender_id,
      $userFriend->friendee_id,
    );
    $query->execute();
    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }

  public static function delete(UserFriend $userFriend) {
    $db = MySqlAdapter::get();
    $query = $db->prepare('
      delete from user_friends
      where friender_id = ?
        and friendee_id = ?
    ');
    $query->bind_param(
      'ss',
      $userFriend->friender_id,
      $userFriend->friendee_id,
    );
    $query->execute();
    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
