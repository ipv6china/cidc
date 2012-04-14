<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Token
 *
 * @author y109
 */
class Etao_Security_Token
{
    const TMP_STORE = 'etao_token.php';
    private $tokenKey;
    private $tokenSalt;
    private $createTime;
    private $expireTime;
    private $tokens = array();

    public function __construct()
    {
        $this->getStore();
        $this->clearToken();
    }

    public function makeToken($tokenSalt, $expireTime=300)
    {
        if($tokenSalt) {
            $value = $this->makeString(20);
            $this->tokens[$value] = array(
                'tokenSalt' => $tokenSalt,
                'createTime' => time(),
                'expireTime' => time() + $expireTime);
            $this->setStore();
            return $value;
        }
    }

    public function isValidToken($tokenSalt='', $tokenKey='', $delete=true)
    {
        $valid = false;
        if(strlen($tokenSalt) > 0 && strlen($tokenKey) > 0) {
            if(key_exists($tokenKey, $this->tokens)) {
                $token = $this->tokens[$tokenKey];
                if($token['tokenSalt'] == $tokenSalt && $token['expireTime'] >= time()) {
                    $valid = true;
                    if($delete) {
                        $this->deleteToken($tokenKey);
                    }
                }
                if($token['expireTime'] < time()) {
                    $this->deleteToken($tokenKey);
                }
            }
        }
        return $valid;
    }

    private function clearToken()
    {
        foreach($this->tokens as $tokenKey => $token)
        {
            if(time() > $token['expireTime']) {
                unset($this->tokens[$tokenKey]);
            }
        }
        $this->setStore();
    }

    public function deleteToken($token)
    {
        if($token) {
            unset($this->tokens[$token]);
            $this->setStore();
        }
    }

    private function setStore()
    {
        $filename = sys_get_temp_dir() . self::TMP_STORE;
        $_data = "<?php\r\n /**\r\n auto created, created on GMT+8 " .
                strftime("%Y-%m-%d %H:%M:%S", time()) . " , do not modify it!\r\n*/ \r\nreturn " .
                var_export($this->tokens, true) . ";\r\n";
        $fp = fopen($filename, 'w');
        fwrite($fp, $_data);
        fclose($fp);
    }

    private function getStore()
    {
        $filename = sys_get_temp_dir() . self::TMP_STORE;
        if(file_exists($filename)) {
            $this->tokens = include $filename;
        }
    }

    /**
     * 生成密码
     */
    private function makeString($len=8, $format='ALL')
    {
        switch($format)
        {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
        }
        mt_srand((double)microtime() * 1000000 * getmypid());
        $string = "";
        while(strlen($string) < $len)
            $string.=substr($chars, (mt_rand() % strlen($chars)), 1);
        return $string;
    }

}