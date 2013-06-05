#!/usr/bin/env php
<?php
chdir(dirname(__FILE__)."/lib/PHPCompactor/");
$basedir = realpath(dirname(__FILE__));

system('php ./src/phpcompactor.php ./../../altax.tmp ./../../src/Altax.php');

$file = file_get_contents($basedir."/altax.tmp");

// $file = "#!/usr/bin/env php\n".$file;
$file = preg_replace("/require_once'[^']+';/", '', $file);
$file = preg_replace("/require_once\"[^\"]+\";/", '', $file);

$file2 = file_get_contents($basedir."/bin/altax");
$file2 = preg_replace("/require_once\s'[^']+';/", '', $file2);

$file = preg_replace("/<\?php/", $file2, $file);

for ($i = 0; $i < 5; $i++) {
  $file = preg_replace("/\n\n/", "\n", $file);
}

echo "\n";
echo "Creating ".$basedir."/altax\n";
file_put_contents($basedir."/altax", $file);

chmod($basedir."/altax", 755);

echo "Deleting ".$basedir."/altax.tmp\n";
unlink($basedir."/altax.tmp");
echo "\n";
echo "Compiling is finished !\n";
echo "\n";



