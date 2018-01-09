<?php
class Dictionary
{
	public $words = array();
	function __construct( $filename)
	{
		$file = fopen($filename,"r");
		while(! feof($file))
		{
			$newWord = fgets($file);
			if( strlen($newWord))
			{
				$nw = new Word($newWord);
				$newKey = $nw->wkey;
				if( array_key_exists($newKey, $this->words) && !in_array($nw->variants[0], $this->words[$newKey]->variants))
				{
					$this->words[$newKey]->variants[] = $nw->variants[0];					
				} 
				else
				{
					$this->words[$newKey] = $nw;
				}
				
			}
		}
		fclose($file);
	}
}


class Word
{
	public /*$word, $transliterated, $onlyalpha,*/ $variants, $wkey;
	function __construct( $word)
	{
		/*$this->word = trim($word);
		$this->transliterated =  transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $this->word);
		$this->variants = array();
		$this->onlyalpha = str_replace( "'", "", $this->transliterated);*/
		$w = trim($word);
		$wt = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $w);
		$wa = str_replace("'", "", $wt);
		$wArr = str_split( $wa);
		sort($wArr);
		$this->wkey = implode("", $wArr);
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