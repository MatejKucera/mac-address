<?php

const DATABASE = 'data/vendors.db';
const DATABASE_WORK = 'data/vendors_work.db';

$dbCurrent = new PDO('sqlite:'.DATABASE); // success

# get current hash
$queryResult = $dbCurrent->query("SELECT checksum FROM checksum")->fetch();
$hashOfCurrentData = $queryResult['checksum'];

# duplicate db
copy(DATABASE, DATABASE_WORK);

$urls = [
    'http://standards-oui.ieee.org/oui/oui.csv',
    'http://standards-oui.ieee.org/oui28/mam.csv',
    'http://standards-oui.ieee.org/oui36/oui36.csv'
    ];


$dbWork = new PDO('sqlite:'.DATABASE_WORK);
$sql = 'INSERT INTO vendors (prefix, company, address) VALUES (:prefix, :company, :address)';
$statement = $dbWork->prepare($sql);


$insert = [];
foreach($urls as $url) {
    $file = fopen($url, 'r');
    fgetcsv($file); // get rid of headers
    while($record = fgetcsv($file)) {
         $insert[] = [
             'prefix' => trim($record[1]),
             'company' => trim($record[2]),
             'address' => trim($record[3])
         ];
    }
}

$hashOfWorkData = md5(serialize($insert));


//echo $hashOfCurrentData . " - ".$hashOfWorkData.PHP_EOL;
if($hashOfCurrentData !== $hashOfWorkData) {

    # update database file
    $dbWork->beginTransaction();
    $dbWork->query('DELETE FROM vendors');
    foreach($insert as $record) {
        $statement->execute($record);
    }
    $dbWork->query("UPDATE checksum SET checksum = '".$hashOfWorkData."'")->execute();
    $dbWork->commit();

    # remove old file, move new
    unlink(DATABASE);
    rename(DATABASE_WORK, DATABASE);

    $tag = exec('git tag');
    $exploded = explode('.', $tag);
    $newTag = $exploded[0].'.'.$exploded[1].'.'.( ((int) $exploded[2]) + 1 );

    //$vendorsPath = realpath(dirname(__DIR__)).'/'.DATABASE;
    $vendorsPath = DATABASE;
    exec('git add '.DATABASE);
    exec('git commit -m \'update of vendors database, checksum of data: '.$hashOfWorkData.'\' '.$vendorsPath);
    exec('git tag '.$newTag);
    exec('git push origin master --tags');

    echo "Updated database. Tag created: ".$newTag.PHP_EOL;
} else {
    echo "No update.".PHP_EOL;
    unlink(DATABASE_WORK);
}

