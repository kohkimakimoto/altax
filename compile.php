#!/usr/bin/env php
<?php


// Chenge vendors for production environment.
system("rm -rf ".__DIR__."/vendor/");
system("composer install --no-dev --no-interaction");

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Finder\Finder;

$pharFile = __DIR__."/altax.phar";

if (file_exists($pharFile)) {
    unlink($pharFile);
}

echo "Starting compiling $pharFile\n";

$phar = new \Phar($pharFile, 0);
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();

$finder = new Finder();
$files = iterator_to_array($finder->files()->exclude('tests')->name('*.php')->in(array('vendor', 'src')));
foreach ($files as $file) {
  echo "Processing: ".$file->getPathName()."\n";
  $phar->addFromString($file->getPathName(), file_get_contents($file));
}

$content = file_get_contents(__DIR__."/bin/altax");
$content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
$phar->addFromString('altax', $content);

$stub = <<<EOL
#!/usr/bin/env php
<?php
Phar::mapPhar('altax.phar');
require 'phar://altax.phar/altax';
__HALT_COMPILER();
EOL;

$phar->setStub($stub);
$phar->stopBuffering();

echo "\n";
unset($phar);
chmod($pharFile, 0755);

echo "Complete!\n";

