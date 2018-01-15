<?php
require_once 'Dictionary.php';

class AnagramFinder
{
	public $dictionary;
	function __construct( $filename, $phrase="", $minWordLen)
	{
		$this->dictionary = new Dictionary( $filename, $phrase, $minWordLen);
	}


	function buildAnagrams()
	{
		$dkeys = array_keys($this->dictionary->words);
		for( $dIdx = 0; $dIdx < count( $dkeys); $dIdx++)
		{
echo("Searching anagrams for $dIdx->[{$dkeys[$dIdx]}]->".implode(",", $this->dictionary->words[$dkeys[$dIdx]]->variants))."\n";
			$origWordObj = $this->dictionary->evalWObj;
			$owFoundAnagrams = $this->getWordAnagrams( $dIdx, $origWordObj, $dkeys);
			if( is_array($owFoundAnagrams) && count($owFoundAnagrams))
			{
//print_r($owFoundAnagrams);
				$wordsComb = array();
				foreach ($owFoundAnagrams as $anaSet)
				{									
					$wordsComb = array_merge( $wordsComb, $this->getAllWordsFromKeysCombined( $anaSet));
				}

				foreach( $this->dictionary->words[$dkeys[$dIdx]]->variants as $curVariant )
				{
					$anaPhrasePrefix = $curVariant;
					foreach( $wordsComb as $combo)
					{
						$anaPhrase = $anaPhrasePrefix;
						foreach( $combo as $comboWord)
						{
							$anaPhrase .= " $comboWord";
						}
						print_r( "->$anaPhrase\n");
					}
				}
			}

		}
	}

	//Get all word variants from dictionary into an array and returns its cartesian product
	function getAllWordsFromKeysCombined( $anagramsKeyList)
	{
		$arrWords = array();
		foreach( $anagramsKeyList as $key)
		{
			$arrWords[] = $this->dictionary->words[$key]->variants;
		}
		if( count($arrWords))
		{
			$cp = $this->getCProduct( 0, $arrWords);
			return $cp;
		}
		else
		{
			return $arrWords;
		}
	}

	//Returns cartesian product for a nested array
	function getCProduct( $curIdx, $nestedArr)
	{
		$productArr = array();
		if( $curIdx === count($nestedArr))
		{
			$productArr[] = array();
		}
		else
		{
			foreach( $nestedArr[$curIdx] as $word)
			{
				foreach( $this->getCProduct( $curIdx+1, $nestedArr) as $innerArr)
				{
					$innerArr[] = $word;
					$productArr[] = $innerArr;
				}
			}

		}
		return $productArr;
	}

	function getWordAnagrams( $curIdx, $curWordObj, $dictionaryKeys)
	{
		if( $curIdx >= count($dictionaryKeys) || strlen($curWordObj->wkey) < $this->dictionary->minWordLen)
		{
			return FALSE;
		}

		
		$curKey = $dictionaryKeys[$curIdx];
		$curWordKey = $curWordObj->wkey;

		//If in current recursion the $curWordObj has a matching key, well return it
		if( $curKey === $curWordKey)
		{
//echo "$curWordKey - $curKey\n";			
			return array( array( $curKey));
		}

		//cases when dictionary key in current index is a subset of $curWordObj
		if( Dictionary::isSubset( $this->dictionary->words[$dictionaryKeys[$curIdx]]->wkeyArr, $curWordObj->wkeyArr) )
		{
			$diff = Dictionary::getDifference( $curWordObj->wkeyArr, $this->dictionary->words[$dictionaryKeys[$curIdx]]->wkeyArr);
			if( count($diff) >= $this->dictionary->minWordLen)
			{
				$nwKey = implode("", $diff);
				$nwObj = new Word( $nwKey);
				//echo ("Key ". $curWordObj->wkey." - ".$this->dictionary->words[$dictionaryKeys[$curIdx]]->wkey." = $nwKey\n");
				$recAnagramsFound = array();
				for( $newIdx = $curIdx+1; $newIdx < count($dictionaryKeys); $newIdx++)
				{
					$nwFoundAnagramKey = $this->getWordAnagrams( $newIdx, $nwObj, $dictionaryKeys);
					if( $nwFoundAnagramKey !== FALSE)
					{
						$updAnaKeys = $this->addWordKeyToFoundSet( $curKey, $nwFoundAnagramKey);
						$recAnagramsFound = array_merge($recAnagramsFound, $updAnaKeys);
					}
				}
				return !empty( $recAnagramsFound)? $recAnagramsFound : FALSE;
			}
		}

		return FALSE;
	}

	function addWordKeyToFoundSet( $newKey, $keysSet)
	{
		$updatedKeysSets = array();
		foreach ($keysSet as $subset ) {
			$subset[] = $newKey;
			$updatedKeysSets[] = $subset;
		}

		return $updatedKeysSets;
	}
}