<?php
/*
 Comsenz Inc.
 This is NOT a freeware, use is subject to license terms
 y109
 Diff.php
 2011-4-20
 */
class Etao_File_Diff {
	/**
	 * 对比源文件夹
	 *  @var array
	 */
	private $_dir1 = '';
	/**
	 * 对比目标文件夹
	 *  @var array
	 */
	private $_dir2 = '';
	/**
	 * 新增的文件
	 * @var array
	 */
	public $new_file = array();
	/**
	 * 新增的文件夹
	 * @var arrray
	 */
	public $new_dir = array();
	/**
	 * 删除的文件
	 * @var array
	 */
	public $delete_file = array();
	/**
	 * 删除的文件夹
	 * @var array
	 */
	public $delete_dir = array();
	/**
	 * 有差别的文件
	 * @var array
	 */
	public $diff_file = array();

	/**
	 * 是否有差异
	 * @var bool
	 */
	public $different = FALSE;

	/**
	 * 排除列表
	 * @var array
	 */
	public $_excludes;

	private $_mark;

	public function __construct($dir1,$dir2,$params=array()) {
		$this->_dir1 = $this->del_postfix($dir1);
		$this->_dir2 = $this->del_postfix($dir2);
		$exclude_tmp = array();
		if (array_key_exists ('exclude', $params)) {
			foreach ($params['exclude'] as $file) {
			 $exclude_tmp[] = self::del_postfix($file);
			}
		}
		$this->_excludes = $exclude_tmp;
	}

	/**
	 * 去除路径末尾的/，并确保是绝对路径
	 *
	 * @param string $file
	 * @return string
	 */
	public static function del_postfix($file) {
		if (!preg_match('#^/#', $file)) {
			throw new Exception('路径必须以/开始');
		}
		$file = preg_replace('#/$#', '', trim($file));
		return $file;
	}

	/**
	 * 公用函数，调用一个递归方法实现比较
	 *
	 * @param int $only_check_has 为1表示不比较文件差异，为0表示还要比较文件的md5校验和
	 */
	public function diff($only_check_has=FALSE){
		$this->_mark ='new';
		$this->process_compare($this->_dir1,  $this->_dir2,  0);
		$this->_mark ='delete';
		//检查第2个路径的多余文件夹或文件
		$this->process_compare($this->_dir2 , $this->_dir1, 1);
	}
	/**
	 * 公用函数，调用一个递归方法实现比较
	 *
	 * @param string $dir1 作为标准的路径
	 * @param string $dir2 对比用的路径
	 * @param int $only_check_has 为1表示不比较文件差异，为0表示还要比较文件的md5校验和
	 */
	function process_compare($dir1, $dir2, $only_check_has){
		$this->_compare_file_folder($dir1,  $dir1, $dir2, $only_check_has);
	}

	/**
	 * 真实的函数，私有函数
	 *
	 * @param string $dir1        路径1，是标准
	 * @param string $base_dir1   不变的参数路径2
	 * @param string $base_dir2   不变的待比较的路径2
	 * @param int $only_check_has 为1表示不比较文件差异，为0表示还要比较文件的md5校验和
	 *
	 */
	private function _compare_file_folder($dir1, $base_dir1, $base_dir2, $only_check_has=FALSE){
		if (is_dir($dir1)) {
			$handle = dir($dir1);
			if ($dh = opendir($dir1)) {
				while ($entry = $handle->read()) {
					if (($entry != ".") && ($entry != "..")  && ($entry != ".svn")){
						$new = $dir1."/".$entry;
						//处理排除列表
						$current_file = substr($new,strlen($base_dir1));

						if (in_array($current_file, $this->_excludes)) {
							continue;
						}
						$other = preg_replace('#^'. $base_dir1 .'#' ,  $base_dir2, $new);
						if(is_dir($new)) {
							if (is_dir($other)) {
								$this->_compare_file_folder($new, $base_dir1,$base_dir2,  $only_check_has) ;
							} else {
								$this->different = TRUE;
								if ($this->_mark === 'new') {
									$this->delete_dir[] = $new;
								} else {
									$this->new_dir[] = $new;
								}
							}

						} else { //如果1是文件，则2也应该是文件
							if (!is_file($other)) {
								$this->different = TRUE;
								if ($this->_mark === 'new') {
									$this->delete_file[] = $new;
								} else {
									$this->new_file[] = $new;
								}
							}elseif ($only_check_has ==0 && ( md5_file($other) != md5_file($new) )  ){
								$this->different = TRUE;
								$this->diff_file[$new] = $other;
							}
						}
					}
				}
				closedir($dh);
			}

		}
	}

	public static function printFileDiff($file_old,$file_new) {
		$html_reg = '/\/|\./i';
		$id =preg_replace($html_reg, '_', $file_old );
		$html ='';
		ob_start(); //打开缓冲区
		echo '<script language="javascript">
function diffWithJS'.$id.'() {
	var base'.$id.' = difflib.stringAsLines($("baseText'.$id.'").value);
	var newtxt'.$id.' = difflib.stringAsLines($("newText'.$id.'").value);
	var sm = new difflib.SequenceMatcher(base'.$id.', newtxt'.$id.');
    var opcodes'.$id.' = sm.get_opcodes();
    var diffoutputdiv'.$id.' = $("diffoutput'.$id.'");
	diffoutputdiv'.$id.'.appendChild(
	diffview.buildView({ baseTextLines:base'.$id.',
		newTextLines:newtxt'.$id.',
		opcodes:opcodes'.$id.',
		baseTextName:"'.$file_old.'",
		newTextName:"'.$file_new.'",
		contextSize:20,
		viewType: 1}));
}
dojo.addOnLoad(diffWithJS'.$id.');
</script>
<textarea id="baseText'.$id.'" style="display:none;width: 600px; height: 300px">'.file_get_contents($file_old).'</textarea>
<textarea id="newText'.$id.'" style="display:none;width: 600px; height: 300px">'.file_get_contents($file_new).'</textarea>
<div id="diffoutput'.$id.'" style="width: 100%"></div>
';

		$html .= ob_get_contents(); //得到缓冲区的内容并且赋值给$info
		ob_clean();
		ob_flush();
		$html .= '<hr/>';
		return $html;
	}
}
?>