<?php

function useMustImage(mixed $file)
{
  $mime = mime_content_type($file);
  return str_contains($mime, 'image');
}
