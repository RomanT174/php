<?php 

set_time_limit(0);

/* SCRIPT SETTINGS */
$host = 'localhost'; // DB HOST
$dbname = 'testdb'; //DB NAME
$table_name = 'domains'; //TABLE NAME
$user = 'root'; // DB USER
$pass = ''; // DB PASSWORD

$dsn = 'mysql:host='.$host;
$pdo = new PDO($dsn, $user, $pass, array(PDO::MYSQL_ATTR_LOCAL_INFILE => true));

$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

if (count($argv)!=2) {
    echo('run: php fill_db.php /path/to/csv/file.csv'.PHP_EOL);
    die();
}
$csv_file = $argv[1];


$sth = $pdo->prepare("CREATE DATABASE IF NOT EXISTS $dbname");
$sth->execute();

//create table if exist
$create_table = 'CREATE TABLE IF NOT EXISTS '.$dbname.'.'.$table_name.' (domain text NOT NULL,  first_seen DATE NOT NULL, last_seen DATE NOT NULL, etld text NOT NULL, id int(11) NOT NULL,time_date_imported TIMESTAMP DEFAULT CURRENT_TIMESTAMP, primary key (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$sth = $pdo->prepare($create_table);
$sth->execute();

$upload = "load data local infile '".$csv_file."'REPLACE into table ".$dbname.'.'.$table_name." fields terminated by ',' enclosed by '\"' lines terminated by '\n' IGNORE 1 LINES  (domain,@fs,@ls,etld,id,time_date_imported) SET first_seen=FROM_UNIXTIME(@fs), last_seen=FROM_UNIXTIME(@ls);";

$sth = $pdo->prepare($upload);
if (!$sth) {
    print_r($pdo::errorInfo());
}
$sth->execute();
echo PHP_EOL."Done!".PHP_EOL;
?>
