<?php
/* 2017年2月21日 星期二
 *  Aes 非对称协议
 */
 namespace hyang\crypt;
 class Aes{
     private $iv;
     private $method = 'aes128';
     // 初始向量  Initialization Vector
     public function create_iv($iv=null)
     {
         $iv = $iv? $iv:rand(100000,999999);
         $iv = sha1($iv);
         $iv = substr($iv,0,16);
         if(strlen($iv) < 16) $iv = $iv.str_repeat(rand(1,9),(16 - strlen($iv)));
         $this->iv = $iv;
         return $iv;
     }
     // 获取 初始向量
     public function getIv(){
         return $this->iv? $this->iv: $this->create_iv();
     }
     // 设置 加密算法
     public function setMethod($method=null){
         if(empty($method) || ($method && !in_array($method,openssl_get_cipher_methods()))) $method = 'aes128';
         $this->method = empty($this->method)? $this->method : 'aes128';
        //  println($this->method);         
         return $this->method;
     }
     // 数据加密
     public function encrypt($data,$password,$method=null)
     {
         $method = $method? $method:$this->setMethod();
        //  println($method);
         $iv = empty($this->iv)? $this->create_iv() : $this->iv;
         return openssl_encrypt($data,$method,$password,false,$iv);
     }
     // 数据解密
     public function decrypt($data,$password,$iv,$method=null)
     {
         $method = $method? $method:$this->setMethod();
         return openssl_decrypt($data,$method,$password,false,$iv);
     }
 }

/***
// 可用 method 

AES-128-CBC AES-128-CFB AES-128-CFB1 AES-128-CFB8 AES-128-ECB AES-128-OFB AES-192-CBC AES-192-CFB AES-192-CFB1 AES-192-CFB8 AES-192-ECB AES-192-OFB AES-256-CBC AES-256-CFB AES-256-CFB1 AES-256-CFB8 AES-256-ECB AES-256-OFB BF-CBC BF-CFB BF-ECB BF-OFB CAST5-CBC CAST5-CFB CAST5-ECB CAST5-OFB DES-CBC DES-CFB DES-CFB1 DES-CFB8 DES-ECB DES-EDE DES-EDE-CBC DES-EDE-CFB DES-EDE-OFB DES-EDE3 DES-EDE3-CBC DES-EDE3-CFB DES-EDE3-CFB1 DES-EDE3-CFB8 DES-EDE3-OFB DES-OFB DESX-CBC IDEA-CBC IDEA-CFB IDEA-ECB IDEA-OFB RC2-40-CBC RC2-64-CBC RC2-CBC RC2-CFB RC2-ECB RC2-OFB RC4 RC4-40 aes-128-cbc aes-128-cfb aes-128-cfb1 aes-128-cfb8 aes-128-ecb aes-128-ofb aes-192-cbc aes-192-cfb aes-192-cfb1 aes-192-cfb8 aes-192-ecb aes-192-ofb aes-256-cbc aes-256-cfb aes-256-cfb1 aes-256-cfb8 aes-256-ecb aes-256-ofb bf-cbc bf-cfb bf-ecb bf-ofb cast5-cbc cast5-cfb cast5-ecb cast5-ofb des-cbc des-cfb des-cfb1 des-cfb8 des-ecb des-ede des-ede-cbc des-ede-cfb des-ede-ofb des-ede3 des-ede3-cbc des-ede3-cfb des-ede3-cfb1 des-ede3-cfb8 des-ede3-ofb des-ofb desx-cbc idea-cbc idea-cfb idea-ecb idea-ofb rc2-40-cbc rc2-64-cbc rc2-cbc rc2-cfb rc2-ecb rc2-ofb rc4 rc4-40

***/