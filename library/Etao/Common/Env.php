<?php

/**
 * 获得客户端的操作系统
 *
 * @access   private
 * @return   void
 */
class Etao_Common_Env
{

    /**
     * 判断是否是Windows系统
     * @return type
     */
    public static function is_win ()
    {
        return (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN');
    }

    /**
     * 是否是Cli模式
     * @return type
     */
    public static function is_cli ()
    {
        return (strtolower (substr (PHP_SAPI, 0, 3)) === 'cli');
    }

    /**
     * 获取换行符
     * @return type
     */
    public static function getEol ()
    {
        return PHP_EOL;
    }

    /**
     * 获取客户端操作系统
     * @return string
     */
    public static function getOS ()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;
        if (eregi ('win', $agent) && strpos ($agent, '95')) {
            $os = 'Windows 95';
        } else if (eregi ('win 9x', $agent) && strpos ($agent, '4.90')) {
            $os = 'Windows ME';
        } else if (eregi ('win', $agent) && ereg ('98', $agent)) {
            $os = 'Windows 98';
        } else if (eregi ('win', $agent) && eregi ('nt 5.1', $agent)) {
            $os = 'Windows XP';
        } else if (eregi ('win', $agent) && eregi ('nt 5', $agent)) {
            $os = 'Windows 2000';
        } else if (eregi ('win', $agent) && eregi ('nt', $agent)) {
            $os = 'Windows NT';
        } else if (eregi ('win', $agent) && ereg ('32', $agent)) {
            $os = 'Windows 32';
        } else if (eregi ('linux', $agent)) {
            $os = 'Linux';
        } else if (eregi ('unix', $agent)) {
            $os = 'Unix';
        } else if (eregi ('sun', $agent) && eregi ('os', $agent)) {
            $os = 'SunOS';
        } else if (eregi ('ibm', $agent) && eregi ('os', $agent)) {
            $os = 'IBM OS/2';
        } else if (eregi ('Mac', $agent) && eregi ('PC', $agent)) {
            $os = 'Macintosh';
        } else if (eregi ('PowerPC', $agent)) {
            $os = 'PowerPC';
        } else if (eregi ('AIX', $agent)) {
            $os = 'AIX';
        } else if (eregi ('HPUX', $agent)) {
            $os = 'HPUX';
        } else if (eregi ('NetBSD', $agent)) {
            $os = 'NetBSD';
        } else if (eregi ('BSD', $agent)) {
            $os = 'BSD';
        } else if (ereg ('OSF1', $agent)) {
            $os = 'OSF1';
        } else if (ereg ('IRIX', $agent)) {
            $os = 'IRIX';
        } else if (eregi ('FreeBSD', $agent)) {
            $os = 'FreeBSD';
        } else if (eregi ('teleport', $agent)) {
            $os = 'teleport';
        } else if (eregi ('flashget', $agent)) {
            $os = 'flashget';
        } else if (eregi ('webzip', $agent)) {
            $os = 'webzip';
        } else if (eregi ('offline', $agent)) {
            $os = 'offline';
        } else {
            $os = 'Unknown';
        }
        return $os;
    }

}

?>