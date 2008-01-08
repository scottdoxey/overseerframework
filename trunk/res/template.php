<?php

###############################################################
#
# Template
#
###############################################################

if (!isset($_GET['norender']) && isset($ob_template)) { ob_start('ob_template'); }

function ob_template($buffer) {
	
	global $ob_template;

	$regs = array();

	preg_match('/<title>(.*)<\/title>/si', $buffer, $regs);
	$ob_template = str_replace('%TITLE%', trim($regs[1]), $ob_template);

	preg_match('/<\/title>(.*)<\/head>/si', $buffer, $regs);
	$ob_template = str_replace('%HEADER%', trim($regs[1]), $ob_template);

	preg_match('/<body>(.*)(<\/body>)/si', $buffer, $regs);
	$ob_template = str_replace('%CONTENT%', trim($regs[1]), $ob_template);

	// $ob_template = ereg_replace('%+[[:alnum:]_]+%', '', $ob_template);

	return $ob_template;

}

###############################################################

?>