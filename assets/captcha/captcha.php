<?php

define("__STRING_LENGTH__", 8);
define("__KEY_LENGTH__", 3);

class tx_captchachaform_captcha
{
	// Same as class name
	protected $prefixId = 'tx_captchachaform_pi1';

	// The extension key.
	protected $extKey = 'captchacha_form';

	protected $_contentUid = 0;

	protected $_errorArray = array();

	protected $securCryptKey = '';

	protected $stringLength = 0;
	protected $keyLength = 0;
	protected $captchaString = array();

	protected $captchaKey = "";

	protected $series = array(
		'letters' => array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'),
		'numbers' => array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
		'symboles' => array('△', '□', '♢', '☆', '■', '▲', '♥', '♣', '♦', '♠', '▼', '▲', '◘', '☻', '☺', '◄', '►'),
		'vowels' => array('A', 'E', 'I', 'O', 'U', 'Y'),
		'specialcharacters' => array('@', '?', '$', '#', '&', '<', '§', '>', ''),
		'accents' => array('à', 'á', 'â', 'é', 'è', 'ê', 'î', 'ò', 'ó', 'ô', 'ù', 'ú', 'û', 'ý'),
	);

	protected $rule = "";
	protected $rules = array(
		array(
			'type' => 'first',
			'rule' => 'rule1',
			'number' => array('3','4','5'),
			'series' => array('letters', 'numbers'),
			'excluded' => array('O', '0', '1', 'l'),
		),
		array(
			'type' => 'last',
			'rule' => 'rule2',
			'number' => array('3','4','5'),
			'series' => array('letters', 'numbers'),
			'excluded' => array('O', '0', '1', 'l'),
		),
		array(
			'type' => 'combi',
			'rule' => 'rule3',
			'numbers' => array('1,3', '2,2', '3,1', '1,4', '2,3', '3,2', '4,1', '1,5', '2,4', '3,3', '4,2', '5,1'),
			'series' => array('letters', 'numbers'),
			'excluded' => array('O', '0', '1', 'l'),
		),
		array(
			'type' => 'unique',
			'rule' => 'rule4',
			'number' => array('4','5'),
			'series' => array('symboles', 'letters'),
		),
		array(
			'type' => 'unique',
			'rule' => 'rule5',
			'number' => array('4','5'),
			'series' => array('letters', 'numbers'),
			'excluded' => array('O', '0', '1', 'l'),
		),
		array(
			'type' => 'unique',
			'rule' => 'rule6',
			'number' => array('4','5'),
			'series' => array('numbers', 'letters'),
			'excluded' => array('O', '0', '1', 'l'),
		),
		array(
			'type' => 'unique',
			'rule' => 'rule7',
			'number' => array('4','5'),
			'series' => array('specialcharacters', 'letters'),
			'excluded' => array('O', '0', '1', 'l'),
		),
		array(
			'type' => 'unique',
			'rule' => 'rule5',
			'number' => array('4','5'),
			'series' => array('letters', 'specialcharacters'),
			'excluded' => array('O', '0', '1', 'l'),
		),
	);

	protected $lang = array(
		'fr' => array(
			'form.captcha.rule1' => 'À des fins de sécurité, veuillez sélectionner les <strong>%s premiers caractères</strong> de la série.',
			'form.captcha.rule2' => 'À des fins de sécurité, veuillez sélectionner les <strong>%s derniers caractères</strong> de la série.',
			'form.captcha.rule3' => 'À des fins de sécurité, veuillez sélectionner les <strong>%s premiers caractères</strong> et les <strong>%s derniers caractères</strong> de la série.',
			'form.captcha.rule3.1' => 'À des fins de sécurité, veuillez sélectionner le <strong>premier caractère</strong> et les <strong>%s derniers caractères</strong> de la série.',
			'form.captcha.rule3.2' => 'À des fins de sécurité, veuillez sélectionner les <strong>%s premiers caractères</strong> et le <strong>dernier caractère</strong> de la série.',
			'form.captcha.rule4' => 'À des fins de sécurité, veuillez sélectionner <strong>tous les symboles</strong> de la série.',
			'form.captcha.rule5' => 'À des fins de sécurité, veuillez sélectionner <strong>toutes les lettres</strong> de la série',
			'form.captcha.rule6' => 'À des fins de sécurité, veuillez sélectionner <strong>tous les chiffres</strong> de la série',
			'form.captcha.rule7' => 'À des fins de sécurité, veuillez sélectionner <strong>tous les caractères spéciaux</strong> de la série',
			'form.captcha.rule8' => 'À des fins de sécurité, veuillez sélectionner <strong>tous les caractères avec un accent</strong> de la série',
			'form.captcha.label' => '',
			'form.require' => 'Champs obligatoires',
			'form.error.field' => 'Vous n\'avez pas saisi correctement ce champ',
			'form.error.global' => 'Le formulaire n\'a pas été saisi correctement.<br />Veuillez corriger les erreurs et valider à nouveau',
			'form.captcha.fieldset' => 'Validation',
			'form.captcha.label' => 'La clé de validation',
			'form.captcha.help' => '',
			'text.number.1' => 'un',
			'text.number.2' => 'deux',
			'text.number.3' => 'trois',
			'text.number.4' => 'quatre',
			'text.number.5' => 'cinq',
			'text.number.6' => 'six',
		)
	);

	// + -----------------------------------------------------------------------
	// Méthode pour générer la chaine
	// + -----------------------------------------------------------------------
	private function makeString($combinaisons)
	{
		$result = array();

		$this->captchaString = array();

		$alphaNumerique = array();
		foreach($combinaisons as $key => $val)
		{
			$alphaNumerique = array_merge($alphaNumerique, $this->series[$val]);
		}
		//$alphaNumerique = array_merge(range("A", "Z"), range(0, 9));;

		do {

			$caractere = $alphaNumerique[rand(0, sizeof($alphaNumerique) - 1)];

			if($caractere != '0' && $caractere != 'O')
			{
				if (ord($caractere)) array_push($this->captchaString, $caractere);
			}

		} while (sizeof($this->captchaString) < $this->stringLength);


		return $result;
	}


	private function generateSecurCryptKey($length = 10)
	{
		$result = '';

		$characters = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));

		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++)
		{
			$result .= $characters[mt_rand(0, $max)];
		}

		$result = md5($result);

		return $result;
	}


	// + -----------------------------------------------------------------------
	// Méthode pour générer la clé
	// + -----------------------------------------------------------------------
	private function generateKey()
	{
		// reinit
		$this->captchaKey = '';
		$this->captchaString = [];
		$this->numbers = array();

		// length string
		$strings = array(8, 9, 10);
		$rang = rand(0, (count($strings) - 1));
		$this->setStringLength($strings[$rang]);
		unset($rang);


		// Select rule by random
		$count = (count($this->rules) -1);

		$rang = rand(0, $count);
		$rule = $this->rules[$rang];

		unset($count);

		// lang rule in global
		$this->rule = $rule['rule'];

		// select number letter by random
		$length = 0;
		if(is_array($rule['number']))
		{
			$rand = rand(0, count($rule['number']) - 1);
			$length = $rule['number'][$rand];
			unset($rand);
		}
		else
		{
			$length = $rule['number'];
		}

		switch($rule['type'])
		{
			case 'first' :

				$this->captchaString = $this->generateString($rule['series']);

				$this->numbers[] = $length;

				$tmp = implode('', $this->captchaString);

				$this->captchaKey = substr($tmp, 0, $length);

				break;

			case 'last' :

				$this->captchaString = $this->generateString($rule['series']);

				$this->numbers[] = $length;

				$tmp = implode('', $this->captchaString);

				$this->captchaKey = substr($tmp, - $length);

				break;

			case 'combi' :

				$this->captchaString = $this->generateString($rule['series']);

				$_numberArray = $rule['numbers'];

				$_tempRang = rand(0, (count($_numberArray) - 1));

				$split = explode(',', $_numberArray[$_tempRang]);

				if($split[0] == '1')
				{
					$this->rule .= '.1';
					$this->numbers[] = $split[1];
				}
				else if($split[1] == '1')
				{
					$this->rule .= '.2';
					$this->numbers[] = $split[0];
				}
				else
				{
					$this->numbers[] = $split[0];
					$this->numbers[] = $split[1];
				}

				$_first = $split[0];
				$_last = $split[1];

				$tmp = implode('', $this->captchaString);

				$this->captchaKey = substr($tmp, 0, $_first);
				$this->captchaKey .= substr($tmp, - $_last);

				break;

			case 'unique' :

				$this->captchaString = $this->generateString($rule['series'][0], $length);

				$this->captchaKey = implode('', $this->captchaString);

				$this->numbers[] = $length;

				$series = $rule['series'];
				array_shift($series);
				$complement = $this->generateString($series, ($this->stringLength - $length));

				$newCombinaison = array_pad($complement, $this->stringLength, "|s|");
				shuffle($newCombinaison);

				$count = 0;
				foreach($newCombinaison as $key => $val)
				{
					if($val == '|s|')
					{
						$newCombinaison[$key] = $this->captchaString[$count];
						$count++;
					}
				}

				//$newCombinaison = array_merge($this->captchaString, $complement);


				$this->captchaString = $newCombinaison;

				unset($newCombinaison);

				break;
		}

		if($_GET['debug'])
		{
			echo 'numero de la regle : '.$rang.'<br />';
			echo 'type de regle : '.$rule['type'].'<br />';
			echo 'nombre de caractère : '.implode(', ', $this->numbers).'<br />';
			echo 'carctères affichés : '.implode(', ', $this->captchaString).'<br />';
			echo 'clé à trouver : '.$this->captchaKey.'<br />';
			//echo $this->rule.'<br />';
		}

		if($this->captchaKey != '')
		{
			$this->securCryptKey = $this->generateSecurCryptKey();

			session_start();
			$_SESSION['tx_captchachaform_captcha_special'][$this->securCryptKey]['expire'] = false;
			$_SESSION['tx_captchachaform_captcha_special'][$this->securCryptKey]['key'] = $this->captchaKey;
		}
	}


	// + -----------------------------------------------------------------------
	// Méthode pour générer la chaine
	// + -----------------------------------------------------------------------
	private function generateString($combinaisons, $length = 0)
	{
		$result = array();

		if(!$length) $length = $this->stringLength;

		$letters = array();
		if(is_array($combinaisons))
		{
			foreach($combinaisons as $key => $val)
			{
				$letters = array_merge($letters, $this->series[$val]);
			}
		}
		else if(is_string($combinaisons))
		{
			$letters = $this->series[$combinaisons];
		}

		$count = count($letters);

		while(count($result) < $length)
		{
			$pos = rand(0, $count);

			$letter = $letters[$pos];
			if(!empty($letter) && !in_array($letter, $result))
			{
				array_push($result, $letter);
			}
		}

		return $result;
	}


	// + -----------------------------------------------------------------------
	// Méthode set pour renseigner la longueur de chaine à afficher
	// + -----------------------------------------------------------------------
	private function setStringLength($valeur)
	{
		$this->stringLength = __STRING_LENGTH__;
		if ((int) $valeur > 0) $this->stringLength = $valeur;
	}


	// + -----------------------------------------------------------------------
	// Méthode set pour renseigner la longueur de clé à gérer
	// + -----------------------------------------------------------------------
	private function setKeyLength($valeur)
	{
		$this->keyLength = __KEY_LENGTH__;
		if ((int) $valeur > 0) $this->keyLength = $valeur;
	}


	public function getSecurCryptKey()
	{
		return $this->securCryptKey;
	}


	public function validate($key = '', $test = '')
	{
		$result = false;

		session_start();

		if(!empty($key) && !empty($test))
		{
			if(isset($_SESSION['tx_captchachaform_captcha_special'][$key]))
			{
				if($_SESSION['tx_captchachaform_captcha_special'][$key]['key'] === $test)
				{
					$result = true;
				}
			}
		}

		return $result;
	}

	public function makeCode()
	{
		$result = array();

		$this->makeString();
		$this->generateKey();

		$result = array(
			'type' => 'captcha'
			, 'rules' => $this->rule
			, 'numbers' => $this->numbers
			, 'items' => $this->captchaString
			, 'error' => ''
			, 'additionalClass' => ''
		);

		if(is_array($this->_errorArray) && count($this->_errorArray) > 0 && array_key_exists('captcha', $this->_errorArray))
		{
			$result['additionalClass'] = 'has-error';
			$result['error'] = $this->_errorArray['captcha'];
		}

		return $result;
	}


	public function makeHtmlCode()
	{
		$result = '';

		$this->setStringLength(__STRING_LENGTH__);
		$this->setKeyLength(__KEY_LENGTH__);

		$this->generateKey();

		foreach($this->numbers as $key => $val)
		{
			$this->numbers[$key] = $this->lang['fr']['text.number.'.$val];
		}

		$text = $this->lang['fr']['form.captcha.'.$this->rule];

		$result = '<div class="require">';
		$result .= '<div class="captcha form-group">';
		if($this->errors)
		{
			$result .= '<p class="help-block error">'.$this->lang['fr']['form.error.field.captcha'].'</p>';
		}
		//$result .= '<p class="obligatoires">'.$this->lang['fr']['form.require'].'</p>';
		$result .= '<p class="text-center">'.vsprintf($text, $this->numbers).'</p>';
		//$result .= ' <label for="'.$this->prefixId.'_captcha" class="control-label">'.$this->lang['fr']['form.captcha.label'].'</label>';
		$result .= '<ul class="serie list-inline list-unstyled text-center">';
		foreach($this->captchaString as $key => $val)
		{
			$result .= '<li><input name="'.$this->prefixId.'[captcha][]" id="'.$this->prefixId.'_captcha_opt'.($key + 1).'" value="'.$val.'" type="checkbox" /><label for="'.$this->prefixId.'_captcha_opt'.($key + 1).'" class="control-label">'.$val.'</label></li>';
		}
		$result .= '</ul>';
		$result .= '</div>';
		$result .= '</div>';

		return $result;
	}
}


$str_json = file_get_contents('php://input'); //($_POST doesn't work here)
$_POST = json_decode($str_json, true); // decoding received JSON to array

$flag = false;

$flagDebug = false;
if(isset($_GET['debug']) && $_GET['debug'] == 'true')
{
    $_POST = $_GET;
    unset($_POST['debug']);

    $flagDebug = true;
}

$allowedValues = array('action', 'page', 'key', 'checkKey', 'type', 'output', 'test');

$arguments = [];
if(!empty($_POST))
{
	$flag = true;
	foreach($_POST as $key => $val)
	{
	    if(in_array($key, $allowedValues)) $arguments[$key] = $val;
	}
}

$result = ['error' => '', 'response' => '', 'html' => '', 'debug' => '', 'checkKey' => ''];

$secure = new tx_captchachaform_captcha();

//$result['debug'] = $_POST;


if(isset($_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS']) && !$flagDebug)
{
	$flag = false;
}


if(!$flag)
{
	echo 'Fault ! Access forbidden';
	exit();
}

switch(trim($arguments['action']))
{
	case 'render' :

		$result['html'] = $secure->makeHtmlCode();
		$result['checkKey'] = $secure->getSecurCryptKey();

		break;

	case 'validate' :

		$result['response'] = $secure->validate($arguments['checkKey'], $arguments['test']);

		if(!$result['response'])
		{
			unset($_SESSION['tx_captchachaform_captcha_special'][$arguments['checkKey']]);

			$result['html'] = $secure->makeHtmlCode();
			$result['checkKey'] = $secure->getSecurCryptKey();
		}

		break;
}

unset($secure);

echo json_encode($result);

?>
