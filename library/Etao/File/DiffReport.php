<?php
/*
 [Discuz!] (C)2001-2011 Comsenz Inc.
 This is NOT a freeware, use is subject to license terms
 y109
 DiffReport.php
 2011-4-25
 */

class Etao_File_DiffReport {
	public static function report($path1,$path2,$params=array()){
		$html = '';
		$html .=  '标准目录: '.$path1.'<br />对比目录: '.$path2;
		if (array_key_exists ('exclude', $params)) {
			foreach ($params['exclude'] as $file) {
			 Etao_File_Diff::del_postfix($file);
			}
		}
		$html .= '<hr><h4>排除列表</h4>';
		foreach ($params['exclude'] as $file) {
			$html .= $file.'<br />';
		}
		$differ = new Etao_File_Diff($path1,$path2,$params);
		$differ->diff();
		if ($differ->different) {
			if (count($differ->new_dir) > 0) {
				$html .=  '<hr><h3>新增了如下文件夹</h3><a name="new_dir"></a>';
				foreach ($differ->new_dir as $dirname) {
					$html .=  '<strong>'.$dirname.'</strong><br />';
					$filenames = Etao_File_Dir::listFile($dirname);
					if (key_exists('file', $filenames)) {
						foreach ($filenames['file'] as $file) {
							$html .=  '<a href="#'.$file.'">'.$file.'</a><br />';
						}
					}
				}
			}
			if (count($differ->new_file) > 0) {
				$html .=  '<hr><h3>新增了如下文件</h3><a name="new_file"></a>';
				foreach ($differ->new_file as $file) {
					$html .=  '<a href="#'.$file.'">'.$file.'</a><br />';
				}
			}
			if (count($differ->delete_dir) > 0) {
				$html .=  '<hr><h3>删除了如下文件夹</h3><a name="delete_dir"></a>';
				foreach ($differ->delete_dir as $dirname) {
					$html .=  '<strong>'.$dirname.'</strong><br />';
					$filenames = Etao_File_Dir::listFile($dirname);
					foreach ($filenames['file'] as $file) {
						$html .=  '<a href="#'.$file.'">'.$file.'</a><br />';
					}
				}
			}
			if (count($differ->delete_file) > 0) {
				$html .=  '<hr><h3>删除了如下文件</h3><a name="delete_file"></a>';
				foreach ($differ->delete_file as $file) {
					$html .=  '<a href="#'.$file.'">'.$file.'</a><br />';
				}
			}
			if (count($differ->diff_file) > 0) {
				$html .=  '<hr><h3>修改了如下文件</h3><a name="diff_file"></a>';
				foreach ($differ->diff_file as $file_old => $file_new) {
					$html .=  '<a href="#'.$file_old.'">'.$file_old.'</a><br />';
				}
				$html .=  '</pre>';
			}


			if (count($differ->new_dir) > 0) {
				$html .=  '<hr>新增的文件夹中的文件内容<pre>';
				foreach ($differ->new_dir as $dir) {
					$file = Etao_File_Dir::printFileInPath($dir);
					$html .=  $file;
				}
				$html .=  '</pre>';
			}
			if (count($differ->new_file) > 0) {
				$html .=  '<hr>新增的文件内容<pre>';
				foreach ($differ->new_file as $new_file) {
					$file = Etao_File_Dir::printFile($new_file);
					$html .=  $file;
				}
				$html .=  '</pre>';
			}
			if (count($differ->delete_dir) > 0) {
				$html .=  '<hr>删除的文件夹中的文件内容<pre>';
				foreach ($differ->delete_dir as $dir) {
					$file = Etao_File_Dir::printFileInPath($dir);
					$html .=  $file;
				}
				echo '</pre>';
			}
			if (count($differ->delete_file) > 0) {
				$html .=  '<hr>删除的文件内容<pre>';
				foreach ($differ->delete_file as $new_file) {
					$file = Etao_File_Dir::printFile($new_file);
					$html .=  $file;
				}
				$html .=  '</pre>';
			}

			if (count($differ->diff_file) > 0) {
				$html .=  '<hr>修改的文件<pre>';
				foreach ($differ->diff_file as $file_old => $file_new) {
					$html .= '<strong><a name="'.$file_old.'">'.$file_old.'</a></strong><br />';
					$html .= Etao_File_Diff::printFileDiff($file_old, $file_new);
				}
				$html .=  '</pre>';
			}

		} else {
			$html = FALSE;
		}
		return $html;
	}

}

?>