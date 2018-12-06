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
$extracted = str_replace('.gz', '', basename($file));

file_put_contents($outfile, fopen($file, 'r') );

if (file_exists($outfile)) {
    echo PHP_EOL.'file downloaded';
} else {
    echo PHP_EOL.'file NOT downloaded';
    die();
}
exec("gzip -d $outfile");


if (file_exists($temp_folder.$extracted)) {
    echo PHP_EOL."extracted:".$temp_folder.$extracted.PHP_EOL;
} else {
    echo PHP_EOL."NOT extracted!";
}