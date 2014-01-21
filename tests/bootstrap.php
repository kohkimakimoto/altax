<?php
if (is_file(__DIR__ . '/../vendor/autoload.php')) {

  require_once __DIR__ . '/../vendor/autoload.php';

} else if (is_file(__DIR__ . '/../../../autoload.php')) {

  require_once __DIR__ . '/../../../autoload.php';

} else if (is_file(__DIR__ . '/../autoload.php')) {

  require_once __DIR__ . '/../autoload.php';

}