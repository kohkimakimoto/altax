#!/usr/bin/env php
<?php

// Embed commit hash to altax source.
$containerSourcePath = __DIR__."/src/Altax/Foundation/Container.php";
$containerSourceBackupPath = __DIR__."/Container.bak.php";
copy($containerSourcePath, $containerSourceBackupPath);
$contents = file_get_contents($containerSourcePath);
$hash = exec("git log --pretty=format:'%H' -n 1");
$contents = str_replace("%commit%", $hash, $contents);
file_put_contents($containerSourcePath, $contents);

// Chenge vendor for production environment.
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
$files = iterator_to_array($finder->files()->exclude(array("tests", "Tests"))->in(array('vendor', 'src')));
foreach ($files as $file) {
  echo "Processing: ".$file->getPathName()."\n";
  $phar->addFromString($file->getPathName(), file_get_contents($file));
}

echo "Packaging ".count($files)." files.\n";

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

echo "Generated $pharFile \n";
unset($phar);
chmod($pharFile, 0755);

echo "File size is ".round(filesize($pharFile) / 1024 / 1024, 2)." MB.\n";
echo "Complete!\n";

// Revert to altax source.
copy($containerSourceBackupPath, $containerSourcePath);
unlink($containerSourceBackupPath);

