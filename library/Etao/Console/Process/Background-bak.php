<?php

class Etao_Console_Process_Background {

	private $processId;		//Process Id
	private $instance;		//Instance to refer the started process//
	private $command;		//command running//
	private $dirName;		//Dirname to store the PIDS and outputs
	private $phpBin;		//PHP Bin path
	private $outFile;		//Output file
	private $pidFile;		//PID File
	private $stats;			//Status Return

	function __construct(){
		$this->processId=	0;	//Process Id //
		$this->instance	=	0;	//Instance of the Object //
		$this->command	=	0;
		$this->dirName	=	"";
		//@TODO 这个要定制下
		$this->phpBin	=	"/usr/local/bin/php";
		$this->stats	=	array();
	}
	/*
	 *
	 * Simple getter and Setter Methods
	 *
	*/

	function setPid($id){
		$this->processId	=	$id; // set Process Id
	}
	function getPid(){
		return $this->processId;  // returns the process id
	}
	function setCommand($cmd){
		$this->command	=	$cmd; // set command to run
	}
	function getLastCommand(){
		return $this->command;	// get the last command run
	}
	function setInstance($id){
		$this->instance	=	$id;	//	Set a progrm reference number for process
	}
	function getInstance(){
		return $this->instance;	//get the instance id
	}
	function setDir($dir){
		$this->dirName	=	$dir;	//Sets the directory for file storage
	}
	function getDir(){
		return $this->dirName;	// get the directory name
	}
	function getOutFile(){
		return $this->outFile;	//Get outfile path
	}
	function getPidFile(){
		return $this->pidFile;	//get pid file path
	}
	/*
	 * get status of process
	 * this will be called after , checkProcess Method
	 * returns status of process, last checked
	*/
	function getStat(){
		return $this->stats;
	}

	/*
	 * Run PHP Background Process
	 * ARGS : $path -> path to php file to run.
	        : $options -> you can set values to a php file in array format,values will be added in environment values
	 * Returns : PID of opened process (0 on fail)
	*/

	function runPhp($path,$options){
		//Create ENV  - values to be read by the program //
		foreach($options as $opt=>$val){
			putenv("{$opt}={$val}");
		}
		$dir	=	$this->getDir();
		$id		=	$this->getInstance();
		$outName	=	"{$id}.log";
		$pidName	=	"{$id}.pid";
		$outputFile	=	$dir.$outName;
		$pidFile	=	$dir.$pidName;
		$this->outFile	=	$outName; //out file
		$this->pidFile	=	$pidName;    //pid file
		$cmd	=	$this->phpBin." {$path}"; // create command //
		$this->setCommand($cmd); //store command //
		$command =	sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputFile, $pidFile);
		exec($command); // execute the Shell command and open a new php program in backgrnd//
		file_put_contents('/var/www/tmp/debug.log', $command,FILE_APPEND);
		//Get Process Id , ID for the opened child process //
		$fp		=	fopen($pidFile,"r");
		$pid	=	fread($fp,filesize($pidFile));
		fclose($fp);
		$pids	=	explode("\n",$pid);
		$processId	=	$pids[count($pids)-2];
		if(!empty($processId)){
			$this->setPid($processId); // successfully created child//
			return $processId;
		}else{
			return 0;		// no child process//
		}
	}

	/*
	 * Check whether process running or not
	 * ARGS : $pid -> process Id
	*/
	function checkProcess($pid){
		$command = "ps -p $pid";
		$this->setCommand($command); //store command //
		$res	=	shell_exec($command);
		$dir	=	$this->getDir();
		$file	=	$dir."{$pid}-temp.txt"; //temp file
		$fp	=	fopen($file,"w");
		fwrite($fp,$res);
		fclose($fp);

		$fp	=	fopen($file,"r");
		$res	=	fread($fp,filesize($file));
		fclose($fp);
		unlink($file)		;
		$rr	=	explode("\n",$res);
		if(count($rr)==2){
			return false;
		}else{
			$this->stats	=	$rr[1];
			return true;
		}
	}

	/*
	 * Kill Process if not finished
	 * ARGS : $pid -> process id , $instance -> instance id
	*/

	function killProcess($pid,$instance){
		echo "Server Killing";
		$dir	=	$this->getDir();
		$outputFile	=	$dir."{$instance}out.txt";
		$pidFile	=	$dir."{$instance}pid.txt";
		$command	=	"kill -9 {$pid}";
		$this->setCommand($command); //store command //
		if($this->checkProcess($pid)){
			$res	=	shell_exec($command);
		}
		if(file_exists($outputFile)){
			unlink($outputFile);
		}
		if(file_exists($pidFile)){
			unlink($pidFile);
		}
		unset($this->processId);
		return true;
	}

	function clear(){
		unset($this->outFile);
		unset($this->pidFile);
		unset($this->processId);
		unset($this->instance);
		unset($this->command);
		unset($this->dirName);
		unset($this->stats);
	}
}
?>