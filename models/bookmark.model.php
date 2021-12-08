<?php

class Bookmark
{
  public function __construct(
    public string $blog_id,
    public string $user_id,
    public string $created_at,
    public ?Blog $blog = null,
    public ?User $user = null,
  ) {
  }

  public static function fromStdClass(stdClass $obj)
  {
    if (!$obj) return null;
    return new Bookmark(
      $obj->blog_id,
      $obj->user_id,
      $obj->created_at,
    );
  }
}
