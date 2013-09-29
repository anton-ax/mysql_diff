<?php 

spl_autoload_extensions('.php');
spl_autoload_register(function ($className) {
	set_include_path('classes/');
	spl_autoload(ucfirst($className));
});

$params = array(
    ''    => 'help',
    'а:'  => 'from:',
    't:'  => 'to:',
    'a:'  => 'alter:',
);

$options = getopt(implode('', array_keys($params)), $params);

if( $options["from"] || !$options["to"] || !$options["alter"]) {
	die ("Use: ./bin/compare.php --from=user1:pass1@host1:3306/db1 --to=user2:pass2@host2:3307/db2 --alter=/tmp/alter.sql\n");
}
$dumpFrom = new Dumper($options["from"]);
if(!$dumpFrom){
	die('ble');
}
$from = $dumpFrom->dump();

$dumpTo = new Dumper($options["to"]);
$to = $dumpTo->dump();

$alter = Diff::generate($from, $to);

file_put_contents($options["alter"], $alter);