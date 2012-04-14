<?php

/**
 * 文件操作类
 */
class Etao_File_Dir
{

    /**
     * 文件列表用于listDirRecursive
     * @var array
     */
    public static $file_lists = array ();
    /**
     * 文本型文件
     */
    public static $txt_files = array (
        'txt', 'php', 'js', 'html', 'htm', 'css', 'phtml', 'xml'
    );

    /**
     * 返回指定路径的文件夹列表
     *
     * @param string $path
     * @param string $appendPath
     * @return array
     */
    public static function getDirectories ($path, $appendPath = false)
    {
        if (is_dir ($path)) {
            $contents = scandir ($path);
            if (is_array ($contents)) {
                $returnDirs = false;
                foreach ($contents as $dir)
                {
                    if (is_dir ($path . '/' . $dir) && $dir != '.' && $dir != '..' && $dir != '.svn') {
                        $returnDirs[] = $appendPath . $dir;
                    }
                }

                if ($returnDirs) {
                    return $returnDirs;
                }
            }
        }
    }

    /**
     * 去除路径末尾的/，并确保是绝对路径
     *
     * @param string $file
     * @return string
     */
    public static function del_postfix ($file)
    {
        if (!preg_match ('#^/#', $file)) {
            throw new Exception ('路径必须以/开始');
        }
        $file = preg_replace ('#/$#', '', trim ($file));
        return $file;
    }

    /**
     * 建文件夹
     *
     * @param unknown_type $path
     */
    public static function make ($path)
    {
        return mkdir ($path, 0755);
    }

    /**
     * 建多级文件夹
     * eg: /my/own/path
     * will create
     * >my
     * >>own
     * >>>path
     *
     * @param string $base
     * @param string $path
     */
    public static function makeRecursive ($base, $path)
    {
        $pathArray = explode ('/', $path);
        if (is_array ($pathArray)) {
            $strPath = null;
            foreach ($pathArray as $path)
            {
                if (!empty ($path)) {
                    $strPath .= '/' . $path;
                    if (!is_dir ($base . $strPath)) {
                        if (!self::make ($base . $strPath)) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
    }

    /**
     * 重命名一个文件夹
     *
     * @param string $source
     * @param string $newName
     */
    public static function rename ($source, $newName)
    {
        if (is_dir ($source)) {
            return rename ($source, $newName);
        }
    }

    /**
     * 复制文件夹
     * @param string $source
     * @param string $target
     */
    public static function copyRecursive ($source, $target)
    {
        if (is_dir ($source)) {
            @mkdir ($target);

            $d = dir ($source);

            while (false !== ($entry = $d->read ()))
            {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                $Entry = $source . '/' . $entry;
                if (is_dir ($Entry)) {
                    Etao_File_Directory_Writer::copyRecursive ($Entry, $target . '/' . $entry);
                    continue;
                }
                copy ($Entry, $target . '/' . $entry);
            }

            $d->close ();
        } else {
            copy ($source, $target);
        }
    }

    /**
     * 删除文件夹
     *
     * @param string $target
     * @param bool $verbose
     * @return bool
     */
    public static function deleteRecursive ($target, $verbose=false)
    {
        $exceptions = array ('.', '..');
        if (!$sourcedir = @opendir ($target)) {
            if ($verbose) {
                echo '<strong>Couldn&#146;t open ' . $target . "</strong><br />\n";
            }
            return false;
        }
        while (false !== ($sibling = readdir ($sourcedir)))
        {
            if (!in_array ($sibling, $exceptions)) {
                $object = str_replace ('//', '/', $target . '/' . $sibling);
                if ($verbose)
                    echo 'Processing: <strong>' . $object . "</strong><br />\n";
                if (is_dir ($object))
                    Etao_File_Dir::deleteRecursive ($object);
                if (is_file ($object)) {
                    $result = @unlink ($object);
                    if ($verbose && $result)
                        echo "File has been removed<br />\n";
                    if ($verbose && (!$result))
                        echo "<strong>Couldn&#146;t remove file</strong>";
                }
            }
        }
        closedir ($sourcedir);


        if ($result = @rmdir ($target)) {
            if ($verbose) {
                echo "Target directory has been removed<br />\n";
                return true;
            }
        } else {
            if ($verbose) {
                echo "<strong>Couldn&#146;t remove target directory</strong>";
                return false;
            }
        }
    }

    /**
     * 获取文件扩展名
     * @param string $file
     * @param bool $dot 是否带点
     */
    public static function getSuffix ($file, $dot=false)
    {
        if ($file === null)
            return false;
        $suffix = strtolower (pathinfo ($file, PATHINFO_EXTENSION));
        if ($dot) {
            $suffix = '.' . $suffix;
        }
        return $suffix;
    }

    /**
     * 查找文件名或是文件内容的数据资料
     * @param $path
     * @param $pattern
     * @param $mode
     * @param $deep
     */
    public static function findFile ($path, $pattern, $mode = 'filename', $deep = 0)
    {
        if (!is_dir ($path))
            return false;
        $path = self::pathReplace ($path);
        $alldata = glob ($path . '*');
        if (!is_array ($alldata))
            return array ();
        if ($mode == 'filename') {
            $finds = preg_grep ($pattern, $alldata);
        } elseif ($mode == 'filedata') {
            foreach ($alldata as $file)
            {
                if (is_file ($file))
                    $data[$file] = file_get_contents ($file);
            }
            $finds = array_keys (preg_grep ($pattern, $data));
        }
        if ($deep) {
            foreach ($alldata as $file)
            {
                if (is_dir ($file))
                    $finds = array_merge ($finds, self::findFile ($file, $pattern, $mode, $deep));
            }
        }
        return $finds;
    }

    /**
     * 取到所有文件和目录列表
     * @param unknown_type $path
     * @param unknown_type $type
     * @param unknown_type $array
     */
    public static function listFile ($path, $type = null, & $array = array ())
    {
        if (!is_dir ($path))
            return false;
        $path = self::pathReplace ($path);
        $alldata = glob ($path . '*');
        if (!is_array ($alldata))
            return $array;
        switch ($type)
        {
            case 'file':
                foreach ($alldata as $data)
                {
                    if (is_dir ($data)) {
                        self::listFile ($data, $type, & $array);
                    } else {
                        $array[] = $data;
                    }
                }
                break;

            case 'dir':
                foreach ($alldata as $data)
                {
                    if (is_dir ($data)) {
                        $array[] = $data;
                        self::listFile ($data, $type, & $array);
                    }
                }
                break;

            default:
                foreach ($alldata as $data)
                {
                    if (is_dir ($data)) {
                        $array['dir'][] = $data;
                        self::listFile ($data, $type, & $array);
                    } else {
                        $array['file'][] = $data;
                    }
                }
                break;
        }

        return $array;
    }

    /**
     * 读取文件大小
     * @param $path
     */
    public static function sizeFile ($path)
    {
        if (!is_dir ($path))
            return false;
        $size = 0;
        $path = self::pathReplace ($path);
        $alldata = glob ($path . '*');
        if (!is_array ($alldata))
            return $size;
        foreach ($alldata as $data)
        {
            if (is_dir ($data)) {
                $size += self::sizeFile ($data);
            } else {
                $size += filesize ($data);
            }
        }
        return $size;
    }

    public static function randMd5 ($value=null)
    {
        if ($value === null) {
            $value = md5 (time () . rand (2, 1048576));
        }
        $dir = substr ($value, 8, 2) . '/' . substr ($value, 23, 2) . '/';
        return $dir;
    }

    /**
     * 转换文件大小
     * @param unknown_type $filesize
     */
    public static function sizeUnit ($filesize)
    {
        if ($filesize >= 1073741824) {
            $filesize = round ($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round ($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            $filesize = round ($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            $filesize = $filesize . ' Bytes';
        }
        return $filesize;
    }

    public static function pathReplace ($path)
    {
        return rtrim (preg_replace ("|[\/]+|", DS, $path), DS) . DS;
    }

    /**
     * 打印文件夹中的所有文本型文件
     */
    public static function printFileInPath ($path)
    {
        $files = self::listFile ($path);
        $html = '';
        if (key_exists ('file', $files)) {
            foreach ($files['file'] as $filename)
            {
                if (in_array (self::getSuffix ($filename), self::$txt_files)) {
                    $html .= self::printFile ($filename);
                } else {
                    $html .= '<strong><a name="' . $filename . '">' . $filename . '</a> (' . self::getSuffix ($filename) . '文件，不输出内容)</strong><br />';
                }
            }
            return $html;
        }
    }

    /**
     * 打印文本型文件
     */
    public static function printFile ($filename)
    {
        $html = '';
        if (file_exists ($filename) && is_readable ($filename)) {
            if (in_array (self::getSuffix ($filename), self::$txt_files)) {
                ob_start (); //打开缓冲区
                $html .= '<strong><a name="' . $filename . '">' . $filename . '</a></strong><br />';
                $html .='<blockquote>';
                highlight_file ($filename);
                $html .= ob_get_contents (); //得到缓冲区的内容并且赋值给$info
                $html .='</blockquote>';
                ob_clean ();
            }
        }

        return $html;
    }

    /**
     * 判断文件夹是否为空
     * @param string $dir
     */
    public static function isEmptyDir ($dir)
    {
        $result = TRUE;
        $d = dir ($dir);
        while (($entry = $d->read ()) !== false)
        {
            if ($entry !== '.' && $entry != '..') {
                $result = FALSE;
                break;
            }
        }
        $d->close ();
        return $result;
    }

    /*   函数   listDirRecursive(   $dirName   =   null   )
     * *   功能   列出目录下所有文件及子目录
     * *   参数   $dirName   目录名称
     * *   返回   目录结构数组   false为失败
     */

    public static function listDirRecursive ($dirName = null)
    {
        if (empty ($dirName)) {
            throw new Exception ("directory   is   empty.");
        } else {
            $dirName = self::del_postfix ($dirName);
        }
        if (is_dir ($dirName)) {
            if ($dh = opendir ($dirName)) {
                $tree = array ();
                while (($file = readdir ($dh)) !== false)
                {
                    if ($file != "." && $file != "..") {
                        $filePath = $dirName . "/" . $file;
//self::$file_lists[] = $filePath;
                        if (is_dir ($filePath)) {//为目录,递归
                            self::$file_lists[] = '[' . $filePath . ']';
                            $tree[$file] = self::listDirRecursive ($filePath);
                        } else {//为文件,添加到当前数组
                            self::$file_lists[] = $filePath;
                            $tree[] = $file;
                        }
                    }
                }
                closedir ($dh);
            } else {
                throw new Exception ("can   not   open   directory   $dirName.");
            }
            return self::$file_lists;
        } else {
            throw new Exception ("$dirName   is   not   a   directory.");
        }
    }

    /**
     *删除过期文件
     * @param type $path
     * @param type $time
     */
    public static function deleteOldFile ($path, $time=60)
    {
        $iterator = new DirectoryIterator ($path);
        foreach ($iterator as $fileinfo)
        {
            if ((time () - $fileinfo->getCTime ()) > $time) {
                @unlink ($path . DIRECTORY_SEPARATOR . $fileinfo->getFilename ());
            }
        }
    }

}