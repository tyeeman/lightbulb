<?php

if (isset($_SERVER['IIS_WasUrlRewritten']) && $_SERVER['IIS_WasUrlRewritten'] == '1' && isset($_SERVER['UNENCODED_URL']) && $_SERVER['UNENCODED_URL'] != '') {
	$requestURI = $_SERVER['UNENCODED_URL'];
} elseif (isset($_SERVER['REQUEST_URI'])) {
	$requestURI = $_SERVER['REQUEST_URI'];
} else {
	die('Lightbulb cannot figure out the request URI...');
}

if (($question_mark = strpos($requestURI, '?')) == true)
	$requestURI = substr($requestURI, 0, $question_mark);
$requestURI = ltrim($requestURI, $site_root);

$blog_requested     = false;
$tag_requested      = false;
$pagination_test    = 0;
$pagination_request = 0;

$ex_req = explode('/', $requestURI);
if ($ex_req[0] == $blog_slug) {
	$blog_requested = true;
	array_shift($ex_req);
	if ($ex_req[0] == $tag_slug) {
		$tag_requested = true;
		$pagination_test = 1;
		array_shift($ex_req);
	}
	if ($enable_pagination && is_numeric($ex_req[$pagination_test])) {
		$pagination_request = $ex_req[$pagination_test];
		unset($ex_req[$pagination_test]);
		$ex_req = array_values($ex_req);
	}
}
$request = implode('/', $ex_req);

if ($tag_requested) $page = new BlogPage($request, $pagination_request);
elseif ($blog_requested && empty($request)) $page = new BlogPage('', $pagination_request);
elseif ($blog_requested) $page = new Page($request, POST_DIR);
else $page = new Page($request);

echo $page->render();