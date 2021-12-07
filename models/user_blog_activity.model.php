<?php

class UserBlogActivity
{
  public function __construct(
    public string $id,
    public string $description,
    public string $user_id,
    public string $blog_id,
    public string $created_at,
  ) {
  }

  public static function fromStdClass(stdClass $obj)
  {
    if (!$obj) return null;
    return new UserBlogActivity(
      $obj->id,
      $obj->description,
      $obj->user_id,
      $obj->blog_id,
      $obj->created_at,
    );
  }
}
