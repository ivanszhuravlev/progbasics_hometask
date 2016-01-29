<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Links</title>
  <link rel="stylesheet" type="text/css" href="../styles/request.css">
</head>

<body>

<?
function format_url($url) {
    
    /*
    ** Добавляем префикс http:// к адресу, если его нет.
    */
    if (stristr($url, "http") === false) {
		$url = "http://" . $url;
	}
    
    /*
    ** Убираем URN (наверное).
    */
    while (substr_count($url, "/") > 2) {
        $url = substr($url, 0, strrpos($url, '/'));
    }
    
    return $url;
}

/*
** Создаем условие к содержанию ссылки.
*/
function condition($link) {
    $condition = !stristr($link, "/news/") === false && 
                 stristr($link, "/category/") === false &&
                 stristr($link, "#") === false;
    
    return $condition;
}

$url = $_POST['field'];


if (isset($url)){

	$url = format_url($url);
    
    echo $url;
    
    /*
    ** Подключаем библиотеку Simple HTML DOM,
    ** создаем объект и подгружаем код страницы.
    */
    include('./lib/simple_html_dom.php');
    
    $html = new simple_html_dom();
    
    $html->load_file($url);
    /*
    ** --------------------
    */
    
    $anchors = $html->find('a');
    
    $suitable_links = array();
    
    foreach ($anchors as $anchor){ 
        if (condition($anchor->href)) {
            if (stristr($anchor->href, "http://") === false && stristr($anchor->href, "https://") === false) {
                $href = $url . $anchor->href;
            } else {
                $href = $anchor->href;
            }
            
            if (!in_array($href, $suitable_links)) {
                array_push($suitable_links, $href);
            }
        }
    }    
    
    
}
else {
	echo "Ошибка! Введите URL.";
}

?>
