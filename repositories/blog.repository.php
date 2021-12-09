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

  public static function getAllApprovedByUsers(array $users)
  {
    $blogs = [];
    foreach ($users as $user) {
      $blogs = [...$blogs, ...BlogRepository::getAllApprovedByUser($user)];
    }
    return $blogs;
  }

  public static function getAllApprovedByUsersPaginated(array $users, int $page = 1)
  {
    $blogs = [];
    foreach ($users as $user) {
      $blogs = [...$blogs, ...BlogRepository::getAllApprovedByUser($user)];
    }

    $blogs = array_map(fn (Blog $blog) => [$blog->created_at => $blog], $blogs);
    ksort($blogs);
    $blogs = array_map(fn ($e) => array_values($e)[0], $blogs);

    $limit = 10;
    $offset = ($page - 1) * $limit;
    return [
      array_slice($blogs, $offset, $limit),
      count($blogs)
    ];
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

  public static function getAllLatestApprovedPaginated(int $page = 1)
  {
    $db = MySqlAdapter::get();

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $query = $db->prepare('
      select *
      from blogs
      where status = \'approved\'
      order by created_at desc, title desc
      limit ?
      offset ?
    ');
    $query->bind_param('ii', $limit, $offset);
    $query->execute();
    $result = $query->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $model = Blog::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $models[] = $model;
    }

    $query = $db->prepare('
      select count(*) as count
      from blogs
      where status = \'approved\'
      order by created_at desc, title desc
    ');
    $query->execute();
    $totalCount = $query->get_result()->fetch_object()->count;

    $db->close();
    return [$models, $totalCount];
  }

  public static function getAllHottestPaginated(int $page = 1)
  {
    $db = MySqlAdapter::get();

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $query = $db->prepare('
      select b.*, count(uba.id) as `count`
      from blogs b
        left join user_blog_activities uba on b.id = uba.blog_id
      where status = \'approved\'
      group by b.id
      order by `count` desc, created_at desc, title desc
      limit ?
      offset ?
    ');
    $query->bind_param('ii', $limit, $offset);
    $query->execute();
    $result = $query->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $model = Blog::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $models[] = $model;
    }

    $query = $db->prepare('
      select count(*) as `count`
      from (
          select b.*, count(uba.id) as `count`
          from blogs b
            left join user_blog_activities uba on b.id = uba.blog_id
          where status = \'approved\'
          group by b.id
          order by `count` desc, created_at desc, title desc
      ) as x;
    ');
    $query->execute();
    $totalCount = $query->get_result()->fetch_object()->count;

    $db->close();
    return [$models, $totalCount];
  }

  public static function getAllApprovedByUser(User $user)
  {
    $db = MySqlAdapter::get();

    $query = $db->prepare('
      select *
      from blogs
      where status = \'approved\'
        and user_id = ?
      order by created_at desc, title desc
    ');
    $query->bind_param('s', $user->id);
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

  public static function getAllApprovedPaginatedByKeyword(int $page = 1, string $keyword = '')
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

    $query = $db->prepare("
      select count(*) as count
      from blogs b
        join users u on b.user_id = u.id
      where status = 'approved'
        and (title like ?
        or content like ?
        or u.name like ?
        or u.email like ?)
    ");
    $query->bind_param('ssss', $keyword, $keyword, $keyword, $keyword);
    $query->execute();
    $totalCount = $query->get_result()->fetch_object()->count;

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
      insert into blogs (
        `id`,
        `title`,
        `content`,
        `image_path`,
        `status`,
        `user_id`,
        `is_premium`,
        `created_at`
      )
      values (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $query->bind_param(
      'ssssssis',
      $blog->id,
      $blog->title,
      $blog->content,
      $blog->image_path,
      $blog->status,
      $blog->user_id,
      $blog->is_premium,
      $blog->created_at,
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
