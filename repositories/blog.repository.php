<?php

class BlogRepository
{
  public static function getAll()
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare('select * from blogs order by created_at desc, title desc');
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

  public static function getAllByKeyword(string $keyword)
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare("
      select b.*
      from blogs b
        join users u on b.user_id = u.id
      where title like '%?%'
        or content like '%?%'
        or u.name like '%?%'
        or u.email like '%?%'
      order by created_at desc, title desc
    ");
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

  public static function getAllApproved()
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare('select * from blogs where status = \'approved\' order by created_at desc, title desc');
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

  public static function getAllApprovedPaginated(int $page = 1)
  {
    $db = MySqlAdapter::get();

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $query = $db->prepare('select * from blogs where status = \'approved\' order by created_at desc, title desc limit ? offset ?');
    $query->bind_param('ii', $limit, $offset);
    $query->execute();
    $result = $query->get_result();

    $models = [];

    while ($obj = $result->fetch_object()) {
      $model = Blog::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $models[] = $model;
    }

    $result = $db->query('select count(*) as count from blogs where status = \'approved\'');
    $totalCount = $result->fetch_object()->count;

    $db->close();
    return [$models, $totalCount];
  }

  public static function getAllApprovedPaginatedByKeyword(int $page = 1, string $keyword)
  {
    $db = MySqlAdapter::get();

    $limit = 10;
    $offset = ($page - 1) * $limit;
    $keyword = "%$keyword%";

    $query = $db->prepare("
      select b.*
      from blogs b
        join users u on b.user_id = u.id
      where status = 'approved'
        and (title like ?
        or content like ?
        or u.name like ?
        or u.email like ?)
      order by created_at desc, title desc
      limit ?
      offset ?
    ");
    $query->bind_param('ssssii', $keyword, $keyword, $keyword, $keyword, $limit, $offset);
    $query->execute();
    $result = $query->get_result();

    $models = [];

    while ($obj = $result->fetch_object()) {
      $model = Blog::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $models[] = $model;
    }

    $totalCount = count($models);

    $db->close();
    return [$models, $totalCount];
  }

  public static function getAllUnapprovedPaginated(int $page = 1)
  {
    $db = MySqlAdapter::get();

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $query = $db->prepare('select * from blogs where status = \'unapproved\' order by created_at desc, title desc limit ? offset ?');
    $query->bind_param('ii', $limit, $offset);
    $query->execute();
    $result = $query->get_result();

    $models = [];

    while ($obj = $result->fetch_object()) {
      $model = Blog::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $models[] = $model;
    }

    $result = $db->query('select count(*) as count from blogs where status = \'unapproved\'');
    $totalCount = $result->fetch_object()->count;

    $db->close();
    return [$models, $totalCount];
  }

  public static function getAllUnapproved()
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare('select * from blogs where status = \'unapproved\' order by created_at desc, title desc');
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

  public static function update(Blog $blog)
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare("
      update blogs set
        `title` = ?,
        `content` = ?,
        `image_path` = ?,
        `status` = ?,
        `user_id` = ?
      where `id` = ?
    ");
    $query->bind_param(
      'ssssss',
      $blog->title,
      $blog->content,
      $blog->image_path,
      $blog->status,
      $blog->user_id,
      $blog->id
    );
    $query->execute();

    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }

  public static function delete(Blog $blog)
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare("delete from blogs where id = ?");
    $query->bind_param('s', $blog->id);
    $query->execute();

    $isSuccess = $query->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
