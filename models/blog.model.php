<?php

class Blog
{
  public function __construct(
    public string $id,
    public string $title,
    public string $content,
    public string $image_path,
    public string $status,
    public string $user_id,
    public string $created_at,
    public ?User $user = null,
  ) {
  }

  public static function fromStdClass(?stdClass $obj)
  {
    if (!$obj) return null;
    return new Blog(
      $obj->id,
      $obj->title,
      $obj->content,
      $obj->image_path,
      $obj->status,
      $obj->user_id,
      $obj->created_at,
    );
  }
}
