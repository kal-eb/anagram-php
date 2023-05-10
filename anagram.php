<?php
include_once 'AnagramFinder.php';

#Comment on production branch
$strToCheck = "poultry outwits ants";
$minWordLen = 4;
$pathToWordsList = $argv[1];

$aF = new AnagramFinder( $pathToWordsList, $strToCheck, $minWordLen);
$aF->buildAnagrams();


/*foreach( $aF->dictionary->words as $key=>$entry)
{
	$str = "[$key]->";
	foreach( $entry->variants as $variant)
	{
		$str .= "$variant,";
	}
	echo "$str\n";
}
echo count($aF->dictionary->words)."\n";*/
