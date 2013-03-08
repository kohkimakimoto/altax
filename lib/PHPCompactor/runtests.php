<?php
$files = glob('tests/*.in.php');
foreach($files as $file) {
	shell_exec('php src/phpcompactor.php .tmpfile "'.escapeshellcmd($file).'"');
	$out = str_replace('in','out',$file);
	echo $file.': ' ;
	if(file_get_contents('.tmpfile') == file_get_contents($out)) {
		echo "Ok.\n";
	} else {
		echo "Fail.\n".file_get_contents('.tmpfile');
	}
}
unlink('.tmpfile');
?>