<?php
define(DEBUG,false);

/**
 * A utility class to help extract TROPHY.TRP files.
 *
 * This main extraction code is a port from TROPHY.TRP Extractor by Red Squirrel
 * (http://www.psp-cheats.it/redsquirrel)
 *
* <code>
 * require_once 'TRPExtractor.class.php';
 * 
 * $trpex = new TRPExtractor();
 * 
 * if ($trpex->extract()) {
 * 		echo "Success!";
 * } else {
 * 		echo "Failed!";
 * }
 * 
 * </code>
 *
 * @author Nuno Sénica <nsenica@gmail.com>
 */
class TRPExtractor {

	/**
	 * Destination path where the output directory will be created.
	 * Defaults to the current path.
	 *
	 * @var string
	 */
	private $destinationPath;
	
	/**
	 * TRP file path which will be extracted.
	 * Defaults to TROPHY.TRP
	 *
	 * @var string
	 */
	private $trpFile;
	
	/**
	 * Directory name which will be created to include extracted files.
	 * Defaults to the NPWRXXXXX_00 code for this TRP file.
	 * 
	 * @var string
	 */
	private $outputDirName;

	/** 
	 * Creates an instance of TRPExtractor class optionally setting input and output parameters.
	 * Filename and destinationPath are both checked for integrity.
	 * 
	 * @param string $filename a string representing the filename of the file to be extracted.
	 * @param string $dest a string representing the destination dir where the $filename will be extracted to.
	 *  
	 * @access public
	 *
	 * @exception InvalidFileNameException thrown if any of the input parameters are invalid.
	 */
	public function __construct($filename = "TROPHY.TRP", $dest = "./") {
	
		if (!is_file($filename) || !is_readable($filename)) {
			throw new Exception("TRPExtractor::construct: Filename is not valid.");
		}
			
		if (!is_dir($dest) || !is_writable($dest)) {
			throw new Exception("TRPExtractor::construct: Invalid destination.");
		}
		
		$this->trpFile = $filename;
		$this->destinationPath = $dest;
	}
	
	/** 
	 * Sets the output dir name where the files are going to be extracted to.
	 * The directory is created upon extraction.
	 * 
	 * @param string $odName 
	 *
	 * @access public
	 *
	 * @exception InvalidFileNameException thrown if output directory name is invalid.
	 */
	public function setOutputDirName($odName) {
		
		if (!preg_match("/^[A-Z0-9-_]+\$/i",$odName)) {
			throw new Exception("TRPExtractor::setOutputDirName: Output directory name is not valid.");
		}
		
		$this->outputDirName = $odName;
	}
	
	/**
	 * Extracts $trpFile into $destinationPath/$outputDirName;
	 *
	 * @see TRPExtractor::trpFile
	 * @see TRPExtractor::$destinationPath
	 * @see TRPExtractor::$outputDirName
	 *
	 * @return boolean true on success, false on failure.
	 *
	 */
	public function extract()
	{

		$ret = true;
	
		// Var. Declarations
		$c = 64;
		$i = 0;

		$destination = $this->destinationPath."/".(isset($this->outputDirName)?$this->outputDirName:time());
		
		//Let's start!
		if (DEBUG) echo("\n\033[1;33mCreating output folder...\033[0m");
		if (mkdir($destination, 0777, true) < 0) { 
			$ret = false;	
			if (DEBUG) echo("\033[32mFAILED!\033[0m");
		}
		else {
			if (DEBUG) echo("\033[32mOK!\033[0m");
		}

		if (DEBUG) echo("\n\033[1;33mOpening input file... \033[1;37m" . $this->trpFile . "\033[0m");
		$f = fopen($this->trpFile,"rb");
		if ($f === false)
		{
			if (DEBUG) echo("\033[32m FAILED!\033[0m");
			if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to open input file!\n");
			$ret = false;
		}
		else {
			if (DEBUG) echo("\033[32m OK!\033[0m");
		}

		if (DEBUG) echo("\n\033[1;33mGetting header size...\033[0m");
		if (fseek($f,100,SEEK_SET) < 0)
		{
			if (DEBUG) echo("\033[32mSEEK FAILED!\033[0m");
			if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get header size!\n");
			fclose($f);
			$ret = false;
		}
		if (($value = fread($f,4)) == false)
		{
			if (DEBUG) echo("\033[32mREAD FAILED!\033[0m");
			if (DEBUG) echo("\n\n\033[32mERROR:\033[0m unable to get header size!\n");
			fclose($f);
			$ret = false;
		}

		$fine = hexdec(bin2hex($value));
		if (DEBUG) echo("\033[32m OK! \033[0m");

		while ($c < $fine)
		{
			if (DEBUG) echo("\n\n\033[1;33mGetting filename...\033[0m");
			if (fseek($f,$c,SEEK_SET) < 0)
			{
				if (DEBUG) echo("\033[32mSEEK FAILED [1]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal filenames!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			if (($s = fread($f,16)) == false)
			{
				if (DEBUG) echo("\033[32mREAD FAILED [1]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal filenames!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			if (DEBUG) echo("\033[1;37m".$s."\033[0m");

			if (DEBUG) echo("\n>>\tGetting file content...");
			if (fseek($f,20,SEEK_CUR) < 0)
			{
				if (DEBUG) echo("\033[32mSEEK FAILED [2]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal file content!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			if (($value = fread($f,4)) == false)
			{
				if (DEBUG) echo("\033[32mREAD FAILED [2]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal file content!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			
			$i = hexdec(bin2hex($value));
		
			if (fseek($f,4,SEEK_CUR) < 0)
			{
				if (DEBUG) echo("\033[32mSEEK FAILED [3]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal file content!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			if (($value = fread($f,4)) == false)
			{
				if (DEBUG) echo("\033[32mREAD FAILED [3]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal file content!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}

			$z = hexdec(bin2hex($value));
						
			if (fseek($f,$i,SEEK_SET) < 0)
			{
				if (DEBUG) echo("\033[32mSEEK FAILED [4]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal file content!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			
			if (($value = fread($f,$z)) == false)
			{
				if (DEBUG) echo("\033[32mREAD FAILED [4]!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to get internal file content!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			
			$h = $value;
			
			if (DEBUG) echo("\033[32mOK!\033[0m");

			if (DEBUG) echo("\n>>\tOpening output file...");
			
			$path = $destination."/".trim($s);
			
			$o = fopen($path,"w");
			if ($o == false)
			{
				if (DEBUG) echo("\033[32mFAILED!\033[0m");
				if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to open output file!\n");
				$c = $fine;
				fclose($f);
				$ret = false;
			}
			else
			{
				if (DEBUG) echo("\033[32mOK!\033[0m");
				if (DEBUG) echo("\n>>\tWriting output file content...");
				if (fwrite($o,$h,$z) == false)
				{
					if (DEBUG) echo("\033[32mFAILED!\033[0m");
					if (DEBUG) echo("\n\n\033[32mERROR\033[0m: unable to write output file content!\n");
					fclose($f);
					fclose($o);
					$c = $fine;
					$ret = false;
				}
				else {
					if (DEBUG) echo("\033[32mDONE!\033[0m");
				}
			}
			fclose($o);
			$c += 64;
		}

		fclose($f);
		if (DEBUG) echo("\n\n\033[1;33mAll done! Bye bye :-)\033[0m\n\n");
				
		if (!$ret) {
			$this->cleanOutputDir($destination);		
		}
		else {
			if (!isset($this->outputDirName) && file_exists($destination."/TROPCONF.SFM")) {
				$xml = simplexml_load_file($destination."/TROPCONF.SFM");
				rename($destination, $this->destinationPath."/".((string) $xml->npcommid));
			}
		}
		
		return $ret;
	}
	
	// TODO: Move to an utility class.
	private function cleanOutputDir($dir) {
	
		# recursively remove a directory
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				rrmdir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}

}


