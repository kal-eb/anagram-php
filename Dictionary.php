<?php
/********
Class Dictionary
To keep all the words from file that are subset of the phrase from which anagrams must be found
*********/
class Dictionary
{
	public $words = array();
	public $evalStr = "";
	public $evalWObj = NULL;
	public $minWordLen	= 0;
	function __construct( $filename, $phrase="", $minWordLen)
	{
		$this->evalStr = $phrase;
		$this->evalWObj = new Word( str_replace(" ", "", $this->evalStr));
		$this->minWordLen = $minWordLen;
		$file = fopen($filename,"r");
		while(! feof($file))
		{
			//Reading a line/word from the file
			$newWord = str_replace( " ", "",  trim( fgets($file)));
			if( strlen($newWord) >= $this->minWordLen)
			{
				$nw = new Word($newWord);
				$newKey = $nw->wkey;
				//Check if the newly read word is a subset of the input phrase so it can be included in the dictionary
				if( Dictionary::isSubSet($nw->wkeyArr, $this->evalWObj->wkeyArr))
				{
					//The key of the new word already exists
					if( array_key_exists($newKey, $this->words) && !in_array($nw->variants[0], $this->words[$newKey]->variants))
					{
						//Adding the new word as a variant of an existent key in dictionary
						$this->words[$newKey]->variants[] = $nw->variants[0];
						unset($nw);
					} 
					else
					{
						//New key found so adding thw whole Word object to the dictionary
						$this->words[$newKey] = $nw;
					}
				}
				else
				{
					//the newly read word is not worthy... get rid of it
					unset($nw);
				}
				
			}
		}
		fclose($file);

		ksort($this->words);
	}

	function getEvalObj()
	{
		return $this->evalWObj;
	}

	//Checks if array1 is subset of array2
	function isSubSet( $array1, $array2)
	{
		//$a2 = $array2;		
		if( count($array1) > count($array2))
		{
			return FALSE;
		}

		foreach( $array1 as $letter )
		{
			$foundIn = array_search($letter, $array2);
			if( $foundIn !== FALSE )
			{
				unset($array2[$foundIn]);
			}
			else
			{
				return FALSE;
			}
		}
		//error_log("Array1:".implode("", $array1)." | TO BE EVALUATED:".implode("", $a2)." Final:".implode("", $array2)."\n");

		return TRUE;
	}

	//Get all elements from array 1 that are not in array 2
	function getDifference( $array1, $array2)
	{
		$copyArray1 = $array1;
		if( count($copyArray1) <= count($array2))
		{
			return array();
		}
		for( $i = 0; $i < count($array2); $i++)
		{
			$foundIn = array_search($array2[$i], $copyArray1);
			if( $foundIn !== FALSE)
			{
				unset( $copyArray1[$foundIn]);
			}
		}
		return $copyArray1;
	}

	//Returns dictionary's Word object corresponding with searchWord's key
	function getWordAnagrams( $searchWord)
	{
		$searchWordObj = new Word( str_replace(" ", "", $searchWord));
		if( !empty( $this->words[$searchWordObj->wkey]))
		{
			return $this->words[$searchWordObj->wkey];
		}
		else
		{
			return FALSE;
		}
	}
}

/****
Class Word to keep the key (alphabeticaly ordered string) from a word and its variations
*****/
class Word
{
	public $variants, $wkey, $wkeyArr;
	function __construct( $word)
	{
		$w = $word;
		$wt = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $w);
		$wa = str_replace("'", "", $wt);
		$this->wkeyArr = str_split( $wa);
		sort($this->wkeyArr);
		$this->wkey = implode("", $this->wkeyArr);
		$this->variants = array( $w);
		if( !in_array($wt, $this->variants))
		{
			$this->variants[] = $wt;
		}
		if( !in_array($wa, $this->variants))
		{
			$this->variants[] = $wa;
		}	
	}
}