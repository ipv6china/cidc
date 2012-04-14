<?php

/*
  [Discuz!] (C)2001-2011 Comsenz Inc.
  This is NOT a freeware, use is subject to license terms
  y109
  Exec.php
  2011-4-22
 */

class Etao_Console_Exec
{

    public static function exec($command, array &$output = null, &$return_var = null)
    {
        exec($command, $output, $return_var);
        return $return_var;
    }

    public static function execBase64($command, array &$output = null, &$return_var = null)
    {
        $command = base64_decode($command);
        exec($command, $output, $return_var);
        return $return_var;
    }

    /**
     *
     * 用exec函数执行系统命令,并返回结果
     * @param string $cmd 命令
     * @param int $retcode 结果
     */
    public static function runCommand($cmd, $nohup=TRUE)
    {
        set_time_limit(0);
        ignore_user_abort(TRUE);
        if($nohup) {
            $cmd = 'nohup ' . $cmd . ' > /dev/null 2>&1 &';
        }
        $logger = Zend_Registry::get('logger');
        $logger->debug(__METHOD__ . $cmd);
        global $disablefunc;
        $disablefunc = @ini_get("disable_functions");
        if(!empty($disablefunc)) {
            $disablefunc = str_replace(" ", "", $disablefunc);
            $disablefunc = explode(",", $disablefunc);
        } else {
            $disablefunc = array();
        }
        $result = "";
        if(!empty($cmd)) {
            if(is_callable("exec") and !in_array("exec", $disablefunc)) {
                exec($cmd, $result);
                $result = implode("\n", $result);
            } elseif(($result = `$cmd`) !== false) {

            } elseif(is_callable("system") and !in_array("system", $disablefunc)) {
                $v = @ob_get_contents();
                @ob_clean();
                system($cmd);
                $result = @ob_get_contents();
                @ob_clean();
                echo $v;
            } elseif(is_callable("passthru") and !in_array("passthru", $disablefunc)) {
                $v = @ob_get_contents();
                @ob_clean();
                passthru($cmd);
                $result = @ob_get_contents();
                @ob_clean();
                echo $v;
            } elseif(is_resource($fp = popen($cmd, "r"))) {
                $result = "null";
                while(!feof($fp))
                {
                    $result .= fread($fp, 1024);
                }
                pclose($fp);
            }
        }
        return $result;
    }

    /**
     *
     * 用exec函数执行系统命令
     * @param string $cmd 命令
     * @param int $retcode 结果
     */
    public static function runCommands($cmd, $nohup = TRUE, $mayReturnNothing = FALSE)
    {
        set_time_limit(0);
        ignore_user_abort(TRUE);
        if($nohup) {
            $cmd = 'nohup ' . $cmd . ' > /dev/null 2>&1 &';
        }
        $logger = Zend_Registry::get('logger');
        $logger->debug(__METHOD__ . $cmd);
        global $lang;

        $output = array();
        $err = false;

        $c = $cmd;

        $descriptorspec = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));

        $resource = proc_open($c, $descriptorspec, $pipes);
        $error = '';

        if(!is_resource($resource)) {
            exit;
        }

        $handle = $pipes[1];
        $firstline = true;
        while(!feof($handle))
        {
            $line = fgets($handle);
            if($firstline && empty($line) && !$mayReturnNothing) {
                $err = true;
            }

            $firstline = false;
            $output[] = rtrim($line);
        }

        while(!feof($pipes[2]))
        {
            $error .= fgets($pipes[2]);
        }

        $error = trim($error);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($resource);

        if(!$err) {
            return $output;
        } else {
            throw new Exception($err);
        }
    }

}

?>