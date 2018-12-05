<?php
set_time_limit(0);

$temp_folder = '/home/roman/phpfile/temp/'; //EMPTY FOLDER FOR TEMP DATA (MUST BE WRITABLE)
/* */

if (count($argv)!=3) {
    echo('run: php download.php http://site.com/file.csv.gz /path/to/save/'.PHP_EOL);
    die();
}

$file = $argv[1];
$temp_folder =  $argv[2];

$outfile = $temp_folder.basename($file);
array_map('unlink', glob($temp_folder."*"));

file_put_contents($outfile, fopen($file, 'r') );

if (file_exists($outfile)) {
    echo PHP_EOL.'file downloaded';
} else {
    echo PHP_EOL.'file NOT downloaded';
    die();
}
exec("gzip -d $outfile");

$files = scandir($temp_folder);
if (!isset($files[2])){
	echo 'no files'; die();
}
echo PHP_EOL."extracted:".$temp_folder.$files[2].PHP_EOL;
