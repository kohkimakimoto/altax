<?php
if (is_file(__DIR__ . '/../vendor/autoload.php')) {

  require_once __DIR__ . '/../vendor/autoload.php';

} elseif (is_file(__DIR__ . '/../../../autoload.php')) {

  require_once __DIR__ . '/../../../autoload.php';

} elseif (is_file(__DIR__ . '/../autoload.php')) {

  require_once __DIR__ . '/../autoload.php';

}
