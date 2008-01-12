<?php

header('Content-type: text/plain');

$files = array();
$files[] = 'res/constants.php';
$files[] = 'res/functions.php';
$files[] = 'res/template.php';
$files[] = 'res/class.db.php';
$files[] = 'res/class.database.php';
$files[] = 'res/class.template.php';
// $files[] = 'res/class.openid.php';

preg_match_all('/build([0-9]+)/', file_get_contents('framework.php'), $matches);

$output = '<?php

###############################################################
#
# Name: Overseer Framework
# Version: 0.2beta r2 build' . ($matches[1][0] +1) . '
# Author: Neo Geek {neo@neo-geek.net}
# Website: http://neo-geek.net/
# Copyright: (c) 2007 Neo Geek, Neo Geek Labs
# Timestamp: ' . date('Y-m-d H:i:s') . '

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
#
###############################################################

?>';

foreach ($files as $file) { $output .= file_get_contents($file); }

$output = str_replace('?><?php', '', $output);

file_put_contents('framework/framework.php', $output);
file_put_contents('framework/framework.phps', $output);

echo $output;

?>