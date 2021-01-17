<?php

namespace Mk2\Validator;

use Mk2\Libraries\Debug;

class Validator{

    protected $context;
    protected $_post;

    /**
     * __construct
     * @param $context
     */
    public function __construct($context){
        $this->context=$context;
    }

    /**
     * verify
     * @param array $post
     * @param string $validateName
     */
	public function verify($post,$validateName=null){

        $this->_post=$post;

        if(!$validateName){
            $validateName="rule";
        }

        if(empty($this->context->{$validateName})){
            return;
        }

        $validate=$this->context->{$validateName};

        // convert rule
        $validate=$this->_convertRule($validate,$post);

        // set validate rule
        $vRule=new ValidateRule($this->_post);

        $response=[];
        foreach($validate as $field=>$v_){

            $value=$this->getValue($field);

            foreach($v_ as $vv_){

                $rule=$vv_["rule"][0];

                $message=null;
                if(!empty($vv_["message"])){
                    $message=$vv_["message"];
                }

                $parameter=null;
                foreach($vv_["rule"] as $ind=>$vvv_){
                    if($ind!=0){
                        if(!empty($vvv_[1])){
                            if(!is_array($vvv_)){
                                if(substr($vvv_,0,1)=="@"){
                                    $vvv_=$this->getValue(substr($vvv_,1));
                                }
                            }
                        }
                        $parameter[]=$vvv_;    
                    }
                }

                $juge=true;
                if(method_exists($vRule,$rule)){
                    $juge=$vRule->{$rule}($value,$parameter);
                }
                else{
                    if(method_exists($this->context,'validate'.ucfirst($rule))){
                        $methodName='validate'.ucfirst($rule);
                        $juge=$this->context->{$methodName}($value,$parameter,$field);
                    }
                }

                if(!$juge){
                    if(empty($response[$field])){
                        $response[$field]=[];
                    }

                    if(!empty($message)){
                        $response[$field][]=$message;
                    }
                    else{
                        $response[$field][]="validate.".$rule;
                    }
                }
            }
        }

        return $response;
    }

    public function getValue($field){

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

    private function _convertRule($validate,$post){

        foreach($validate as $name=>$value){
            $names=explode(".",$name);
            $enabled=false;
            $count=null;
            $buff=$post;
            $newName=null;
            $afterNames=null;
            foreach($names as $ind=>$n_){
                if(!empty($buff[$n_])){
                    $buff=$buff[$n_];
                }
                if($n_=="*"){
                    $enabled=true;
                    if(!empty($buff)){
                        $count=count($buff);
                    }
                    $afterName=$names[$ind+1];
                    break;
                }
                else{
                    if($ind){
                        $newName.=".";                   
                    }
                    $newName.=$n_;
                }
            }

            if($enabled){
                for($c=0;$c<$count;$c++){
                    $validate[$newName.".".$c.".".$afterName]=$value;
                }
                unset($validate[$name]);
            }
        }

        foreach($validate as $name=>$value){

            if(is_string($value)){
                $values=explode("|",$value);
                $validate[$name]=[];
                foreach($values as $v_){
                    $v_=explode(":",$v_);
                    $validate[$name][]=[
                        "rule"=>$v_,
                    ];
                }
            }
            else{
                foreach($value as $n2=>$v_){
                    if(is_string($v_["rule"])){
                        $v_["rule"]=[$v_["rule"]];
                    }
                    $validate[$name][$n2]=$v_;
                }
            }

            $names=explode(".",$name);

            if(count($names)>=2){
                $_validateBuff=$validate[$name];
                $_name="";
                $ind=0;
                $valueCount=0;
                $enable=false;
                $nameBuff=[];
                foreach($names as $n_){
                    if($n_=="*"){
                        if($_name){
                            $_value=$this->getValue($_name);
                            if(is_array($_value)){
                                $valueCount=count($_value);
                                $enable=true;        
                            }
                        }
                    }
                    else{
                        if($valueCount){
                            for($v1=0;$v1<$valueCount;$v1++){
                                $nameBuff[]=$_name.$v1.".".$n_;
                            }    
                        }
                        
                        $_name.=$n_;
                    }

                    if($ind!=0){
                        $_name.=".";
                    }
                    $ind++;
                }

                if($nameBuff){

                    foreach($nameBuff as $n_){
                        $validate[$n_]=$_validateBuff;
                    }
    
                    unset($validate[$name]);
                }
            }

        }

        return $validate;
    }

    /**
     * addRule
     * @param $argv
     */
    public function addRule(...$argv){
        if(count($argv)==2){
            $this->_addRule($argv[0],null,$argv[1],null);
        }
        else if(count($argv)==3){
            $this->_addRule($argv[0],null,$argv[1],$argv[2]);
        }
        else if(count($argv)==4){
            $this->_addRule($argv[0],$argv[1],$argv[2],$argv[3]);
        }

        return $this;
    }

    private function _addRule($field,$name,$rule,$message){

        if(empty($this->context->rule)){
            $this->context->rule=[];
        }
        if(empty($this->context->rule[$field])){
            $this->context->rule[$field]=[];
        }

        if($name){
            $this->context->rule[$field][$name]=[
                "rule"=>$rule,
                "message"=>$message,
            ];
        }
        else{
            $this->context->rule[$field][]=[
                "rule"=>$rule,
                "message"=>$message,
            ];
        }

    }
    
    /**
     * deleteRule
     * @param string $field
     * @param string $name
     */
    public function deleteRule($field,$name=null){

        if($name){
            unset($this->context->rule[$field][$name]);
        }
        else{
            unset($this->context->rule[$field]);
        }

        return $this;
    }

    /**
     * getVCache
     * @param string $validateName
     */
    public function getVCache($validateName=null){
        
        if(!$validateName){
            $validateName='rule';
        }

        return base64_encode($this->{$validateName});

    }

}