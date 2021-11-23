<?php

function usePrettyDate(string $date) {
  return date('d F Y', strtotime($date));
}
