<?php
require_once("classes/TRPExtractor.class.php");

$trp = new TRPExtractor("testfiles/TROPHY.TRP", "output");

$trp->setOutputDirName("fddf");

if ($trp->extract()) {
	echo "\nSUCCESS\n";
}
else {
	echo "\nFAILED\n";
}

?>
