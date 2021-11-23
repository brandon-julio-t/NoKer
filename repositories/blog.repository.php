<?php

class BlogRepository
{
  public static function getAll()
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare('select * from blogs order by created_at desc');
    $query->execute();
    $result = $query->get_result();

    $models = [];

    while ($obj = $result->fetch_object()) {
      $model = Blog::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $models[] = $model;
    }

    $db->close();
    return $models;
  }

  public static function getOneById(string $id)
  {
    $db = MySqlAdapter::get();
    $query = $db->prepare('select * from blogs where id = ?');
    $query->bind_param('s', $id);
    $query->execute();
    $obj = $query->get_result()->fetch_object();
    $model = Blog::fromStdClass($obj);
    $db->close();
    if ($model) $model->user = UserRepository::getOneById($model->user_id);
    return $model;
  }

  public static function create(Blog $blog)
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare("
      insert into blogs (`id`, `title`, `content`, `image_path`, `status`, `user_id`, `created_at`)
      values (?, ?, ?, ?, ?, ?, now())
    ");
    $query->bind_param(
      'ssssss',
      $blog->id,
      $blog->title,
      $blog->content,
      $blog->image_path,
      $blog->status,
      $blog->user_id
    );
    $query->execute();

    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
