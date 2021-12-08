<?php

class BookmarkRepository
{
  public static function getAllByUser(User $user) {
    $db = MySqlAdapter::get();
    $q = $db->prepare('
      select *
      from bookmarks
      where user_id = ?
    ');
    $q->bind_param('s', $user->id);
    $q->execute();
    $result = $q->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $model = Bookmark::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $model->blog = BlogRepository::getOneById($model->blog_id);
      $models[] = $model;
    }

    $db->close();
    return $models;
  }

  public static function create(Bookmark $bookmark) {
    $db = MySqlAdapter::get();
    $q = $db->prepare('
      insert into bookmarks (`user_id`, `blog_id`, `created_at`)
      values (?, ?, ?)
    ');
    $q->bind_param('sss', $bookmark->user_id, $bookmark->blog_id, $bookmark->created_at);
    $q->execute();
    $isSuccess = $q->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }

  public static function delete(Bookmark $bookmark) {
    $db = MySqlAdapter::get();
    $q = $db->prepare('
      delete from bookmarks
      where `user_id` = ?
        and `blog_id` = ?
    ');
    $q->bind_param('ss', $bookmark->user_id, $bookmark->blog_id);
    $q->execute();
    $isSuccess = $q->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
