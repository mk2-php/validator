<?php

/**
 * ===================================================
 * 
 * PHP Framework "Mk2" - Mk2-Validator
 *
 * ValidateRule
 * 
 * URL : https://www.mk2-php.com/
 * 
 * Copylight : Nakajima-Satoru 2021.
 *           : Sakaguchiya Co. Ltd. (https://www.teastalk.jp/)
 * 
 * ===================================================
 */

namespace Mk2\Validator;

class ValidateRule{

	private $_post;

	/**
	 * __construct
	 * @param $post
	 */
	public function __construct($post){
		$this->_post=$post;
	}

    /**
     * required
     * @param string $field
     */
    private function getValue($field){

        $value=$this->_post;

        $fields=explode(".",$field);

        foreach($fields as $f_){
            if(empty($value[$f_])){
                return null;
            }    

            $value=$value[$f_];
        }

        return $value;
	}
	
    /**
     * required
     * @param string $value
     * @param array $parameters
     */
    public function required($value,$parameters){

        if($value){
            return true;
        }

        if($value===0 || $value==="0"){
            return true;
        }
            
        return false;
	}

	/**
	 * requiredIf
     * @param string $value
     * @param array $parameters
	 */
	public function requiredIf($value,$parameters){

		$targetFieldName=$parameters[0];

		$targetValue=$this->getValue($targetFieldName);
		
		array_shift($parameters);
		
		$juge=true;
		foreach($parameters as $p_){

			if($targetValue == $p_){
				$juge=false;
			}
		}

		if($juge){
			return true;
		}

		return $this->required($value,null);
	}

	/**
	 * requiredWith
     * @param string $value
     * @param array $parameters
	 */
	public function requiredWith($value,$parameters){

		foreach($parameters as $v_){
			$targetValue=$this->getValue($v_);
			
			if(!$this->required($targetValue,null)){
				return true;
			}
		}

		return $this->required($value,null);
	}

	/**
	 * requiredWithOr
     * @param string $value
     * @param array $parameters
	 */
	public function requiredWithOr($value,$parameters){

		$throght=true;
		foreach($parameters as $v_){
			$targetValue=$this->getValue($v_);
			
			if($this->required($targetValue,null)){
				$throght=false;
				break;
			}
		}

		if($throght){
			return true;
		}

		return $this->required($value,null);
	}

	/**
	 * confirmed
     * @param string $value
     * @param array $parameters
	 */
	public function confirmed($value,$parameters){

		$targetValue=$this->getValue($parameters[0]);

		return $this->equal($value,[$targetValue]);
	}
    
    /**
     * alphaNumric
     * @param string $value
     * @param array $parameters
     */
    public function alphaNumeric($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if($parameters){
            foreach($parameters as $p_){
				$value=str_replace($p_,"",$value);
            }
		}

		$reg="/^[a-zA-Z0-9]+$/";

		if(preg_match($reg, $value)){
			return true;
		}

        return false;
    }

    /**
     * numeric
     * @param string $value
     * @param array $parameters
     */
    public function numeric($value,$parameters){

		if(!isset($value)){
			return true;
		}

        if($parameters){
            foreach($parameters as $p_){
				$value=str_replace($p_,"",$value);
            }
        }

        $reg="/^[0-9]+$/";

		if(preg_match($reg, $value)){
			return true;
		}

        return false;
    }

    /**
     * length
     * @param string $value
     * @param array $parameters
     */
    public function length($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(mb_strlen($value)==$parameters[0]){
			return true;
		}

        return false;
    }

	/**
     * minLength
     * @param string $value
     * @param array $parameters
     */
    public function minLength($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(mb_strlen($value)>=$parameters[0]){
			return true;
		}

        return false;
    }

	/**
     * maxLength
     * @param string $value
     * @param array $parameters
     */
    public function maxLength($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(mb_strlen($value)<=$parameters[0]){
			return true;
		}

        return false;
    }

	/**
     * betweenLength
     * @param string $value
     * @param array $parameters
     */
    public function betweenLength($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(mb_strlen($value)>=$parameters[0] && mb_strlen($value)<=$parameters[1]){
			return true;
		}
        
        return false;
    }

	/**
     * value
     * @param string $value
     * @param array $parameters
     */
    public function value($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if((int)$value==(int)$parameters[0]){
			return true;
		}
    
        return false;
    }

	/**
     * minValue
     * @param string $value
     * @param array $parameters
     */
    public function minValue($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if((int)$value>=(int)$parameters[0]){
			return true;
		}
        
        return false;
    }

	/**
     * maxValue
     * @param string $value
     * @param array $parameters
     */
    public function maxValue($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if((int)$value<=(int)$parameters[0]){
			return true;
        }
        
		return false;
    }

	/**
     * betweenValue
     * @param string $value
     * @param array $parameters
     */
    public function betweenValue($value,$parameters){

		if(!isset($value)){
			return true;
		}

        if(
            (int)$value>=(int)$parameters[0] && 
            (int)$value<=(int)$parameters[1]
        ){
			return true;
		}
        
        return false;
    }

	/**
     * selectedCount
     * @param string $value
     * @param array $parameters
	 */
	public function selectedCount($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(count($value)==(int)$parameters[0]){
			return true;
		}

		return false;
	}

	/**
     * minSelectedCount
     * @param string $value
     * @param array $parameters
	 */
	public function minSelectedCount($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(count($value)>=(int)$parameters[0]){
			return true;
		}

		return false;
	}

	/**
     * maxSelectedCount
     * @param string $value
     * @param array $parameters
	 */
	public function maxSelectedCount($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(count($value)<=(int)$parameters[0]){
			return true;
		}

		return false;
	}

	/**
     * betweenSelectedCount
     * @param string $value
     * @param array $parameters
	 */
	public function betweenSelectedCount($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(
			count($value)>=(int)$parameters[0] &&  
			count($value)<=(int)$parameters[1]
		){
			return true;
		}

		return false;
	}

	/**
     * equal
     * @param string $value
     * @param array $parameters
     */
    public function equal($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if((string)$value===(string)$parameters[0]){
			return true;
		}
    
        return false;
    }


	/**
     * like
     * @param string $value
     * @param array $parameters
     */
    public function like($value,$parameters){

		if(!isset($value)){
			return true;
		}
		
		if(strpos($value,$parameters[0])>-1){
			return true;
        }
        
		return false;
	}
	
	/**
     * any
     * @param string $value
     * @param array $parameters
     */
    public function any($value,$parameters){

		if(!isset($value)){
			return true;
        }

		foreach($parameters[0] as $p_){
            if((string)$value===(string)$p_){
                return true;
			}
			
        }
		return false;
    }

	/**
     * date
     * @param string $value
     * @param array $parameters
     */
    public function date($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(@date_format(date_create($value),"U")){
			return true;
        }
        
		return false;
    }

    /**
     * minDate
     * @param string $value
     * @param array $parameters
     */
	public function minDate($value,$parameters){

		if(!isset($value)){
			return true;
		}

		$target=@date_format(date_create($value),"U");
		$jum=@date_format(date_create($parameters[0]),"U");

		if($target>=$jum){
			return true;
		}

		return false;
	}

    /**
     * maxDate
     * @param string $value
     * @param array $parameters
     */
	public function maxDate($value,$parameters){

		if(!isset($value)){
			return true;
		}

		$target=@date_format(date_create($value),"U");
		$jum=@date_format(date_create($parameters[0]),"U");

		if($target<=$jum){
			return true;
		}

		return false;
    }

    /**
     * betweenDate
     * @param string $value
     * @param array $parameters
     */
	public function betweenDate($value,$parameters){

		if(!isset($value)){
			return true;
		}

		$target=@date_format(date_create($value),"U");
		$start=@date_format(date_create($parameters[0]),"U");
		$exit=@date_format(date_create($parameters[1]),"U");

		if($target>=$start && $target<=$exit){
			return true;
		}

		return false;
	}


    /**
     * isInt
     * @param string $value
     * @param array $parameters
     */
	public function isInt($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(intval($value)){
			return true;
        }
        
		return false;
	}

    /**
     * isBool
     * @param string $value
     * @param array $parameters
     */
	public function isBool($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if($value==0 || $value==1){
			return true;
		}

		return false;
	}

    /**
     * isEmail
     * @param string $value
     * @param array $parameters
     */
	public function isEmail($value,$parameters){

		if($value==""){
			return true;
		}

		if($value=="0"){
			return false;
		}

		if(!preg_match("|^[0-9a-z_./?-]+@([0-9a-z_./?-]+\.)+[0-9a-z-]+$|",$value)){
			return false;
		}

		return true;
    }

    /**
     * isTel
     * @param string $value
     * @param array $parameters
     */
	public function isTel($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(preg_match('/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/i',$value)){
			return true;
		}

		if(preg_match('/^[0-9]{6,15}$/i',$value)){
			return true;
		}

		return false;
	}

    /**
     * isIp
     * @param string $value
     * @param array $parameters
     */
	public function isIp($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(preg_match("/(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])/",$value)){
			return true;
		}

		return false;
	}

    /**
     * isIp
     * @param string $value
     * @param array $parameters
     */
	public function isUrl($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(preg_match("/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i",$value)){
			return true;
		}

		return false;
	}

    /**
     * Regex
     * @param string $value
     * @param array $parameters
     */
	public function Regex($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(preg_match($parameters[0],$value)){
			return true;
		}

		return false;
	}

    /**
     * isZipJP
     * @param string $value
     * @param array $parameters
     */
	public function isZipJP($value,$parameters){

		if(!isset($value)){
			return true;
		}

		if(preg_match("/^([0-9]{3}-[0-9]{4})?$|^[0-9]{7}+$/i",$value)){
			return true;
		}

		return false;
	}

    /**
     * isKatakana
     * @param string $value
     * @param array $parameters
     */
	public function isKatakana($value,$parameters){

		if(empty($value)){ return true; }

		$value=str_replace("　","",$value);
		$value=str_replace(" ","",$value);

		if(preg_match("/^[ァ-ヶー]+$/u", $value)){
			return true;
		}

		return false;
	}

    /**
     * isHiragana
     * @param string $value
     * @param array $parameters
     */
	public function isHiragana($value,$parameters){

		if(empty($value)){ return true; }

		$value=str_replace("　","",$value);
		$value=str_replace(" ","",$value);

		if(preg_match("/^[ぁ-ん]+$/u", $value)){
			return true;
		}

		return false;
	}

}