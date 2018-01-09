<?php
print_r( $argv);
include_once 'Dictionary.php';

$dictionary = new Dictionary( $argv[1]);

foreach( $dictionary->words as $key=>$entry)
{
	$str = "[$key]->";
	foreach( $entry->variants as $variant)
	{
		$str .= "$variant,";
	}
	echo "$str\n";
}
echo count($dictionary->words)."\n";
?>