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

	preg_match('/<head>(.*)<\/head>/si', $buffer, $regs);
	$regs[1] = preg_replace('/<title>(.*)<\/title>/si', $regs[1]);
	$ob_template = str_replace('%HEAD%', trim($regs[1]), $ob_template);

	preg_match('/<body>(.*)(<\/body>)/si', $buffer, $regs);
	$ob_template = str_replace('%BODY%', trim($regs[1]), $ob_template);

	// $ob_template = ereg_replace('%+[[:alnum:]_]+%', '', $ob_template);

	return $ob_template;

}

###############################################################

?>