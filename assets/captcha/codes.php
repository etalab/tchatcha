<?php

class Base32 {

   private static $map = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
        'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
        '='  // padding char
    );

   private static $flippedMap = array(
        'A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4', 'F'=>'5', 'G'=>'6', 'H'=>'7',
        'I'=>'8', 'J'=>'9', 'K'=>'10', 'L'=>'11', 'M'=>'12', 'N'=>'13', 'O'=>'14', 'P'=>'15',
        'Q'=>'16', 'R'=>'17', 'S'=>'18', 'T'=>'19', 'U'=>'20', 'V'=>'21', 'W'=>'22', 'X'=>'23',
        'Y'=>'24', 'Z'=>'25', '2'=>'26', '3'=>'27', '4'=>'28', '5'=>'29', '6'=>'30', '7'=>'31'
    );

    /**
     *    Use padding false when encoding for urls
     *
     * @return base32 encoded string
     * @author Bryan Ruiz
     **/
    public static function encode($input, $padding = true) {
        if(empty($input)) return "";
        $input = str_split($input);
        $binaryString = "";
        for($i = 0; $i < count($input); $i++) {
            $binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
        }
        $fiveBitBinaryArray = str_split($binaryString, 5);
        $base32 = "";
        $i=0;
        while($i < count($fiveBitBinaryArray)) {
            $base32 .= self::$map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];
            $i++;
        }
        if($padding && ($x = strlen($binaryString) % 40) != 0) {
            if($x == 8) $base32 .= str_repeat(self::$map[32], 6);
            else if($x == 16) $base32 .= str_repeat(self::$map[32], 4);
            else if($x == 24) $base32 .= str_repeat(self::$map[32], 3);
            else if($x == 32) $base32 .= self::$map[32];
        }
        return $base32;
    }

    public static function decode($input) {
        if(empty($input)) return;
        $paddingCharCount = substr_count($input, self::$map[32]);
        $allowedValues = array(6,4,3,1,0);
        if(!in_array($paddingCharCount, $allowedValues)) return false;
        for($i=0; $i<4; $i++){
            if($paddingCharCount == $allowedValues[$i] &&
                substr($input, -($allowedValues[$i])) != str_repeat(self::$map[32], $allowedValues[$i])) return false;
        }
        $input = str_replace('=','', $input);
        $input = str_split($input);
        $binaryString = "";
        for($i=0; $i < count($input); $i = $i+8) {
            $x = "";
            if(!in_array($input[$i], self::$map)) return false;
            for($j=0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@self::$flippedMap[@$input[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";
            }
        }
        return $binaryString;
    }
}

	public function makeHtmlCode()
	{
		$result = '';

		$this->setStringLength(__STRING_LENGTH__);
		$this->setKeyLength(__KEY_LENGTH__);

		$this->generateKey();

		$secur = '';
		$countLetters = rand(5, 15);
		$positions = [];

		$text = $this->lang['fr'][$this->rule];

		$securCryptKey = 'a42b';

		$result = '<div class="require">';
		$result .= '<div class="captcha form-group {data.additionalClass}">';
		$result .= '<f:if condition="{data.error}"><p class="help-block error"><f:translate key="{data.error}" extensionName="captchacha_form" /></p></f:if>';
		$result .= '<p class="obligatoires"><f:translate key="form.captcha.help" extensionName="captchacha_form" /></p>';
		$result .= '<p class="text-center">'.vsprintf($text, $this->numbers).'</p>';
		$result .= ' <label for="{settings.prefix}_captcha" class="control-label sr-only {data.labelClass}"><f:render partial="Require" /><f:translate key="form.captcha.label" extensionName="captchacha_form" /></label>';
		$result .= '<ul class="serie list-inline list-unstyled text-center">';
		foreach($this->stringValue as $val)
		{

			//echo 'md5 : '.md5($val).'<br />';

			//$secur = $this->securCrypt(md5($val), $securCryptKey);

			//echo $secur.'<br />';

			$result .= '<li><input name="{settings.prefix}[captcha][]" id="{data.type}_opt{iterator.cycle}" value="'.$val.'" type="checkbox" /><label for="{data.type}_opt{iterator.cycle}" class="control-label">'.$val.'</label></li>';

			//$secur = $this->securDecrypt($secur, $securCryptKey);

			//echo $secur.'<br />';

			//exit();

		}
		$result .= '</ul>';
		$result .= '</div>';
		$result .= '</div>';

		return $result;
	}


	private function securCrypt($value, $cryptKey)
	{
		$result = null;

		/*
		Table d'addition

		0 + 0 = 0
		0 + 1 = 1
		1 + 0 = 1
		1 + 1 = 10 (on pose 0 et on retient 1)
		1 + 1 + 1 = 11 (on pose 1 et on retient 1)
		*/

		echo '------- Crypt <br />';

		$temp1 = $this->hexbin($value);
		$temp2 = $this->hexbin($cryptKey);

		$temp1 = 1101010011;
		$temp2 = 101110;

		$len1 = strlen($temp1);
		$len2 = strlen($temp2);

		// echo strlen($temp1).'<br />';
		// echo strlen($temp2).'<br />';

		if($len1 > $len2)
		{
			$temp2 = str_pad($temp2, $len1, "0", STR_PAD_LEFT);
		}
		else
		{
			$temp1 = str_pad($temp1, $len2, "0", STR_PAD_LEFT);
		}

		// echo 'temp1 : '.$temp1.'<br />';
		// echo 'temp2 : '.$temp2.'<br />';

		$retenue = '0';
		$resultat = '';
		$position = $len1;

		while ($position > 0)
		{
			$position -= 1;

		    $chiffre_a = substr($temp1, $position, 1);
		    $chiffre_b = substr($temp2, $position, 1);

		    $temp = '';
			if($retenue == '1') $temp = '1 + ';
		    $temp .= $chiffre_a.' + '.$chiffre_b;

		    if($temp === '0 + 0')
		    {
		    	$retenue = '0';
		    	$somme = '0';
		    }
		    else if($temp === '0 + 1')
		    {
		    	$retenue = '0';
		    	$somme = '1';
		    }
		    else if($temp === '1 + 0')
		    {
		    	$retenue = '0';
		    	$somme = '1';
		    }
		    else if($temp === '1 + 0 + 0')
		    {
				$retenue = '0';
				$somme = '1';
		    }
		    else if($temp === '1 + 1' || $temp === '1 + 0 + 1' || $temp === '1 + 1 + 0')
		    {
				$retenue = '1';
				$somme = '0';
		    }
		    else if($temp === '1 + 1 + 1')
		    {
				$retenue = '1';
				$somme = '1';
		    }
		    else
		    {
		    	$retenue = '0';
		    	$somme = '0';
		    }

		   	echo '(pos : '.$position.') '.$temp.' = '.$somme.' => '.$retenue.'<br />';

			$resultat = $somme.$resultat;
		}

		$temp3 = $resultat;

		/*
 		echo 'val1 : '.$val1.'<br />';
		echo 'val2 : '.$val2.'<br />';
		echo 'temp1 : '.$temp1.'<br />';
		echo 'temp2 : '.$temp2.'<br />';
		echo 'temp3 : '.$temp3.'<br />';
		echo $this->binhex($temp1).'<br />';
		echo $this->binhex(intval($temp2)).'<br />';
		echo $this->binhex($temp3).'<br />';
		echo '<br /><br />';
		*/

		// https://www.mathematiquesfaciles.com/cgi2/myexam/voir2r.php?id=114424
		// http://www.groupeisf.net/automatismes/Numeration/Numeration_binaire/Ressources/Logique_combinatoire/html/01/28-ess0102006.htm

		//11000000011
		//1101010011

		$result = $this->binhex($temp3);

		return $result;
	}


	private function securDecrypt($value, $cryptKey)
	{
		$result = null;

		echo '------- Decrypt<br />';

		/*
		Soustraction en binaire
		- Règle n° 1 : 0 - 0 = 0
		- Règle n° 2 : 1 - 1 = 0
		- Règle n° 3 : 1 - 0 = 1
		- Règle n° 4 : 0 - 1 = 1 avec retenue.
		*/

		$temp1 = $this->hexbin($value);
		$temp2 = $this->hexbin($cryptKey);

		$len1 = strlen($temp1);
		$len2 = strlen($temp2);

		// echo strlen($temp1).'<br />';
		// echo strlen($temp2).'<br />';

		if($len1 > $len2)
		{
			$temp2 = str_pad($temp2, $len1, "0", STR_PAD_LEFT);
		}
		else
		{
			$temp1 = str_pad($temp1, $len2, "0", STR_PAD_LEFT);
		}

		// echo 'temp1 : '.$temp1.'<br />';
		// echo 'temp2 : '.$temp2.'<br />';

		$retenue = 0;
		$resultat = '';
		$position = $len1;

		while ($position > 0)
		{
			$position -= 1;

		    $chiffre_a = substr($temp1, $position, 1);
		    $chiffre_b = substr($temp2, $position, 1);

		    if($retenue == 1) $chiffre_a = 0;

		   	$somme = $chiffre_a - $chiffre_b;

		    echo '(pos : '.$position.') '.$chiffre_a.' '.$chiffre_b.' = '.$somme.' => '.$retenue.'<br />';

			if($somme == -1)
			{
				$retenue = 1;
				$somme = 1;
			}
			else
			{
				$retenue = 0;
			}

			$resultat = $resultat.''.$somme;
		}

		$temp3 = $resultat;

		/*
 		echo 'val1 : '.$val1.'<br />';
		echo 'val2 : '.$val2.'<br />';
		echo 'temp1 : '.$temp1.'<br />';
		echo 'temp2 : '.$temp2.'<br />';
		echo 'temp3 : '.$temp3.'<br />';
		echo $this->binhex($temp1).'<br />';
		echo $this->binhex(intval($temp2)).'<br />';
		echo $this->binhex($temp3).'<br />';
		echo '<br />';
		*/

		$result = $this->binhex($temp3);

		return $result;
	}


	private function secur($value, $code, $mode = 'crypt')
	{
		$result = null;

		//echo $mode.'<br />';

		/*
		Addition en binaire
		- Règle n° 1 : 0 + 0 = 0
		- Règle n° 2 : 0 + 1 = 1
		- Règle n° 3 : 1 + 0 = 1
		- Règle n° 4 : 1 + 1 = 10 (on pose 0 et on retient 1)
		- Règle n° 5 : 1 + 1 + 1 = 11 (on pose 1 et on retient 1)
		*/

		/*
		Soustraction en binaire
		- Règle n° 1 : 0 - 0 = 0
		- Règle n° 2 : 1 - 1 = 0
		- Règle n° 3 : 1 - 0 = 1
		- Règle n° 4 : 0 - 1 = 1 avec retenue.
		*/

		$temp1 = $this->hexbin($value);
		$temp2 = $this->hexbin($code);

		$len1 = strlen($temp1);
		$len2 = strlen($temp2);

		// echo strlen($temp1).'<br />';
		// echo strlen($temp2).'<br />';

		if($len1 > $len2)
		{
			$temp2 = str_pad($temp2, $len1, "0", STR_PAD_LEFT);
		}
		else
		{
			$temp1 = str_pad($temp1, $len2, "0", STR_PAD_LEFT);
		}

		// echo 'temp1 : '.$temp1.'<br />';
		// echo 'temp2 : '.$temp2.'<br />';

		$retenue = 0;
		$resultat = '';
		$position = $len1;

		while ($position > 0)
		{
			$position -= 1;

		    $chiffre_a = substr($temp1, $position, 1);
		    $chiffre_b = substr($temp2, $position, 1);

		    if($mode == 'decrypt')
		    {
		    	if($retenue == 1) $chiffre_a = 0;

		   		$somme = $chiffre_a - $chiffre_b;
		   	}
		   	else
		   	{
		   		$somme = $chiffre_a + $chiffre_b + $retenue;
		   	}

		    if($mode == 'decrypt')
		    {
		   		echo '(pos : '.$position.') '.$chiffre_a.' '.$chiffre_b.' = '.$somme.' => '.$retenue.'<br />';
		   	}

		    if($mode == 'crypt')
		    {
				if($somme == 3)
				{
					$retenue = 1;
					$somme = 1;
				}
				else if($somme == 2)
				{
					$retenue = 1;
					$somme = 0;
				}
				else
				{
					$retenue = 0;
				}
			}

		    if($mode == 'decrypt')
		    {
				if($somme == -1)
				{
					$retenue = 1;
					$somme = 1;
				}
				else
				{
					$retenue = 0;
				}
		    }

			$resultat = $resultat.''.$somme;


		}

		$temp3 = $resultat;

	    if($mode == 'decrypt')
	    {
	 		echo 'val1 : '.$val1.'<br />';
			echo 'val2 : '.$val2.'<br />';
			echo 'temp1 : '.$temp1.'<br />';
			echo 'temp2 : '.$temp2.'<br />';
			echo 'temp3 : '.$temp3.'<br />';
			echo $this->binhex($temp1).'<br />';
			echo $this->binhex(intval($temp2)).'<br />';
			echo $this->binhex($temp3).'<br />';
			echo '<br />';
		}

		$result = $this->binhex($temp3);

		return $result;
	}





	private function hexbin($hex)
	{
		$bin = '';

		for($i=0;$i<strlen($hex);$i++)
		{
			$bin.=str_pad(decbin(hexdec($hex{$i})),4,'0',STR_PAD_LEFT);
		}

		return $bin;
	}


	private function binhex($bin)
	{
		$hex = '';

		for($i = strlen($bin)-4;$i>=0;$i-=4)
		{
			$hex .= dechex(bindec(substr($bin,$i,4)));
		}

		return strrev($hex);
	}

?>
