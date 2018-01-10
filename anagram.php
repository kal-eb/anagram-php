<?php
include_once 'Dictionary.php';


$strToCheck = "poultry outwits ants";
$dictionary = new Dictionary( $argv[1], $strToCheck);

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
