<?php
/*
  +----------------------------------------------------------------------+
  | PHP QA GCOV Website                                                  |
  +----------------------------------------------------------------------+
  | Copyright (c) The PHP Group                                          |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Nuno Lopes <nlopess@php.net>                                 |
  +----------------------------------------------------------------------+
*/

if (!defined('IN_GCOV_CODE')) exit;

$inputfile = "./$version/valgrind.inc";
$raw_data  = @file_get_contents($inputfile);
$data      = unserialize($raw_data);


if (!$raw_data) {
	$content = "<p>Sorry, but this data isn't available at this time.</p>";
	return;

} elseif (isset($_GET['file'])) {
	$file = $_GET['file'];

	if (isset($data[$file])) {
		$data   = $data[$file];
		$file   = htmlspecialchars($file);
		$title  = htmlspecialchars($data[0]);
		$script = highlight_php_numbered($data[1]);
		$report = htmlspecialchars($data[2]);

		$appvars['page']['title'] = "PHP: $version Valgrind Report for $file";
		$appvars['page']['head']  = "Valgrind Report for $file ('$title')";

		$content = <<< HTML
<h2>Script</h2>
$script
<h2>Report</h2>
<pre>$report</pre>
HTML;

	} else {
		$content = "<p>Invalid file ID.</p>\n";
	}

} elseif ($data) {

	$content = '<p><b>'.count($data) . " valgrind reports:</b></p>\n";
	$old_dir = '';

	$content .= <<< HTML
<table border="1">
HTML;

	foreach ($data as $path => $entry) {
		$dir     = htmlspecialchars(dirname($path));
		$file    = htmlspecialchars(basename($path));
		$urlfile = htmlspecialchars(urlencode($path));
		$title   = htmlspecialchars($entry[0]);
		$type    = htmlspecialchars($entry[1]);

		if ($dir !== $old_dir) {
			$old_dir = $dir;
			$content .= <<< HTML
<tr>
 <td colspan="3" align="center"><b>$dir</b></td>
</tr>
<tr>
 <td><b>File</b></td>
 <td><b>Name</b></td>
</tr>
HTML;
		}

		$content .= <<< HTML
<tr>
 <td><a href="/viewer.php?version=$version&amp;func=valgrind&amp;file=$urlfile">$file</a></td>
 <td>$title</td>
</tr>
HTML;

	}

	$content .= <<< HTML
</table>
HTML;

} else {
	$content = "<p>Congratulations! Currently there are no valgrind reports!</p>\n";
}

$content .= footer_timestamp(@filemtime($inputfile));
