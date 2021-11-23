<?php

class Storage
{
  public static function save(mixed $file)
  {
    $path = '/storage/' . useUuid() . '_' . $file['name'];
    $storagePath = __DIR__ . '/../' . $path;
    $isMoved = move_uploaded_file($file['tmp_name'], $storagePath);
    return [$isMoved, $path];
  }

  public static function exists(string $path)
  {
    $storagePath = __DIR__ . '/../' . $path;
    return file_exists($storagePath);
  }
}
