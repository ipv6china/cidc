<?php

class Etao_Common_Util
{

    public static function array_to_string(array $source_array)
    {
        $r = '';
        self::a2s($r, $source_array);
        return $r;
    }

    private static function a2s(&$r, array &$a)
    {
        $f = false;
        $i = 0;
        $r.= 'array(';
        foreach($a as $k => $v)
        {
            if($f)
                $r.=',';
            $j = is_numeric($k);
            self::o2s($r, $k, $v, $i, $j);
            $f = true;
            if($j && $k >= $i)
                $i = $k + 1;
        }
        $r.=')';
    }

    private static function o2s(&$r, $k, $v, $i, $j)
    {
        if($k !== $i) {
            if($j)
                $r.="$k=>";
            else
                $r.="'$k'=>";
        }
        if(is_array($v))
            self::a2s($r, $v);
        else if(is_numeric($v))
            $r.=$v;
        else
            $r.="'" . str_replace(array("\\", "'"), array("\\\\", "\'"), $v) . "'";
    }

    /**
     * 关联数组转字符串
     * @param $map
     * @param $s
     * @param $g
     * @return string
     */
    public static function map2str($map, $s='&', $g='=')
    {
//var_dump($map);
        $str = "";
        foreach($map as $key => $value)
        {
            if(!empty($str)) {
                $str .= $s;
            }
            $str .= $key . $g . urlencode($value);
        }
        return $str;
    }

    public static function getCurrentUser()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Etao_Auth_Storage_UCenter());
        $current_user = null;
        if($auth->hasIdentity()) {
            $current_user = Zend_Auth::getInstance()->getIdentity();
        }
        return $current_user;
    }

    /**
     * @package     BugFree
     * @version     $Id: FunctionsMain.inc.php,v 1.32 2005/09/24 11:38:37 wwccss Exp $
     *
     *
     * Return part of a string(Enhance the function substr())
     *
     * @author                  Chunsheng Wang <wwccss@263.net>
     * @param string  $String  the string to cut.
     * @param int     $Length  the length of returned string.
     * @param booble  $Append  whether append "...": false|true
     * @return string           the cutted string.
     */
    public static function subStr($String, $Length, $Append = false)
    {
        if(strlen($String) <= $Length) {
            return $String;
        } else {
            $I = 0;
            while($I < $Length)
            {
                $StringTMP = substr($String, $I, 1);
                if(ord($StringTMP) >= 224) {
                    $StringTMP = substr($String, $I, 3);
                    $I = $I + 3;
                } elseif(ord($StringTMP) >= 192) {
                    $StringTMP = substr($String, $I, 2);
                    $I = $I + 2;
                } else {
                    $I = $I + 1;
                }
                $StringLast[] = $StringTMP;
            }
            $StringLast = implode("", $StringLast);
            if($Append) {
                $StringLast .= "...";
            }
            return $StringLast;
        }
    }

    /**
     * Var_dump 加强
     * @param type $var
     */
    public static function dump($var)
    {
        echo '<pre>';
        print_r($var);
    }

    /**
     * 写入数据库前转义字符
     * @param String $data
     * @return String
     */
    public static function sqlEscapeString($data)
    {
        if(get_magic_quotes_gpc()) {
            $data = stripslashes($data);
        }
        return mysql_real_escape_string($data);
    }

    /**
     * 用 array_map() 调用 mysql_real_escape_string 清理数组
     * @param array $data
     * @return array
     */
    public static function sqlEscapeArray($data)
    {
        return (is_array($data)) ? array_map(array('Etao_Common_Util', 'sqlEscapeArray'), $data) : self::sqlEscapeString($data);
    }

    /**
     * 读出转义字符后反转义
     * @param String $data
     * @return String
     */
    public static function sqlEscapeStringRe($data)
    {
        if(isset($data)) {
            return strlen($data) > 0 ? stripslashes($data) : $data;
        } else {
            return '';
        }
    }

    /**
     * 用 array_map() 调用 stripslashes 反转义
     * @param array $data
     * @return array
     */
    public static function sqlEscapeArrayRe($data)
    {
        return (is_array($data)) ? array_map(array('Etao_Common_Util', 'sqlEscapeArrayRe'), $data) : self::sqlEscapeStringRe($data);
    }

    public static function htmlentitiesArray($data)
    {
        return (is_array($data)) ? array_map('htmlspecialchars', $data) : htmlspecialchars($data);
    }

    /**
     * 高亮关键字
     */
    public static function highlightKeyWords($keywords, $subject)
    {
        $keywords = (array)$keywords;
        foreach($keywords as $key)
        {
            $subject = str_ireplace($key, '<font color="red"><strong>' . $key . '</strong></font>', $subject);
        }
        return $subject;
    }

    static public function array2string($array, $sep=",", $out="'", $escape=true)
    {
        if(!empty($array)) {
            $r = '';
            foreach($array as $value)
            {
                if($escape) {
                    $r .= $out . self::sqlEscapeString($value) . $out . $sep;
                } else {
                    $r .= $out . $value . $out . $sep;
                }
            }
            if($out) {
                return substr($r, 0, -strlen($out));
            } else {
                return $r;
            }
        } else {
            return '';
        }
    }

    /**
     * 把 update 数组转化为String用于记录日志
     * @param type $arr_new
     * @param type $arr_old
     * @return type
     */
    public static function arrayUpdateToString($arr_new, $arr_old)
    {
        $r = self::array_export(self::my_array_diff($arr_new, $arr_old));
        return $r;
    }

    private function my_array_diff($array_new, $array_old)
    {
        $diff = array_diff($array_new, $array_old);
        $result = array();
        foreach($diff as $key => $value)
        {
            $result[$key] = isset($array_old[$key]) ? $array_old[$key] . ' > ' . $value : '+ ' . $value;
        }
        return $result;
    }

    private function array_export($var)
    {
        if(is_array($var)) {
            $toImplode = array();
            foreach($var as $key => $value)
            {
                $toImplode[] = $key . ':' . self::array_export($value);
            }
            $code = implode(',', $toImplode);
            return $code;
        } else {
            return '[' . $var . ']';
        }
    }

    /**
     * 格式化 Mac地址
     * @param type $mac
     * @return type
     */
    public static function formatMacAddress($mac)
    {
        $_str = preg_replace('/\W+|_/', '', $mac);
        $str = '';
        for($i = 0; $i < strlen($_str); $i++)
        {
            if($i % 2 == 0 & $i > 0) {
                $str.= ':';
            }
            $str .= $_str[$i];
        }
        if(strlen($str) == 17) {
            return $str;
        } else {
            return false;
        }
    }

    /**
     * 生成密码
     */
    public static function mkpasswd($len=8, $format='ALL')
    {
        switch($format)
        {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
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
        $password = "";
        while(strlen($password) < $len)
            $password.=substr($chars, (mt_rand() % strlen($chars)), 1);
        return $password;
    }

}
