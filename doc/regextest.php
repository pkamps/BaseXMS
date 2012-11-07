<?php 

$test = array( '/:fips/a/b',
		       '/:fips/a/b/',
		       '/:fips/a/',
		       '/:fips/',
               '/a/b',
		       '/:fips',
		       '/:fips' );

$regex = '(\/:(?<context>.*?)|)((?<path>\/.*?)|)(\/|)$';

foreach( $test as $entry )
{
	echo 'ENTRY ' . $entry . ": \n";
	preg_match('(\G' . $regex . ')', $entry, $matches, null, 0);
	
	echo 'context: ' . $matches[ 'context' ] . "\n";
	echo 'path: ' . $matches[ 'path' ] . "\n";

	echo "\n\n";
}

?>