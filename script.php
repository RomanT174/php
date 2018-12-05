<?php 
/* SCRIPT SETTINGS */
$host = 'localhost'; // DB HOST
$dbname = 'testdb'; //DB NAME
$table_name = 'domains'; //TABLE NAME
$user = 'root'; // DB USER
$pass = '1'; // DB PASSWORD
$temp_folder = '/home/roman/phpfile/temp/'; //EMPTY FOLDER FOR TEMP DATA (MUST BE WRITABLE)
/* */

if (count($argv)!=2) {
    echo('run: php script.php http://file.csv.gz'.PHP_EOL);
    die();
}

set_time_limit(0);

//remove the files
array_map('unlink', glob($temp_folder."*"));

$dsn = 'mysql:host='.$host;
$pdo = new PDO($dsn, $user, $pass, array());

//$file = 'https://ausdomainledger.net/au-domains-latest.csv.gz';
$file = $argv[1];

$outfile = $temp_folder.basename($file);

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


$sth = $pdo->prepare("CREATE DATABASE IF NOT EXISTS $dbname");
$sth->execute();

//create table if exist
$create_table = 'CREATE TABLE IF NOT EXISTS '.$dbname.'.'.$table_name.' ( id int(11) NOT NULL, domain text NOT NULL, first_seen date NOT NULL, last_seen date NOT NULL, etld text NOT NULL,time_date_imported date NOT NULL, primary key (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$sth = $pdo->prepare($create_table);
$sth->execute();

$sql = 'INSERT INTO '.$dbname.'.'.$table_name.' (id, domain, first_seen, last_seen, etld, time_date_imported ) VALUES (?, ?, ?, ?, ?, NOW())
ON DUPLICATE KEY UPDATE domain = VALUES(domain), first_seen = VALUES(first_seen), last_seen = VALUES(first_seen), etld = VALUES(etld), time_date_imported = VALUES(time_date_imported)';
$add_domain = $pdo->prepare($sql);


$f = fopen($temp_folder.$files[2], 'r');
fgetcsv($f); //skip the fist line
$added_cnt = 0;
echo PHP_EOL.'filling the db';
while (($res = fgetcsv($f)) !== false){
	$add_domain->execute(array($res[4], $res[0], date('Y-m-d H:i:s', $res[1]), date('Y-m-d H:i:s', $res[2]),$res[3]));
    $added_cnt++; 
}

echo PHP_EOL.'Added: '.$added_cnt.' domains'.PHP_EOL;

fclose($f);

//remove the files
array_map('unlink', glob($temp_folder."*"));


?>
