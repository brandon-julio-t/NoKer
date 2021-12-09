<?php

class UserBlogActivityRepository
{
  public static function getAllActivitiesInThisMonthByUser(User $user)
  {
    $db = MySqlAdapter::get();
    $q = $db->prepare('
      select *
      from `user_blog_activities`
      where `user_id` = ?
        and year(`created_at`) = year(now())
        and month(`created_at`) = month(now())
    ');
    $q->bind_param('s', $user->id);
    $q->execute();
    $result = $q->get_result();

    $models = [];
    while ($obj = $result->fetch_object()) {
      $model = UserBlogActivity::fromStdClass($obj);
      $model->user = UserRepository::getOneById($model->user_id);
      $model->blog = BlogRepository::getOneById($model->blog_id);
      $models[] = $model;
    }

    $db->close();
    return $models;
  }

  public static function create(UserBlogActivity $model)
  {
    $db = MySqlAdapter::get();
    $q = $db->prepare('
      insert into `user_blog_activities` (`id`, `description`, `user_id`, `blog_id`, `created_at`)
      values (?, ?, ?, ?, ?)
    ');
    $q->bind_param(
      'sssss',
      $model->id,
      $model->description,
      $model->user_id,
      $model->blog_id,
      $model->created_at,
    );
    $q->execute();
    $isSuccess = $q->affected_rows > 0;
    $db->close();
    return $isSuccess;
  }
}
