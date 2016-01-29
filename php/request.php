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

/*
** Заполняем массив ссылками с $pages_count страниц.
*/
function search_anchors($dom_object, $url, $pages_count) {
    
    $suitable_links = array();
    $next_url = $url;
    
    for ($current_page = 1; $current_page <= $pages_count; $current_page++) {
        
        $url = $next_url;
        
        /*
        ** Это функция из библиотеки Simple HTML DOM,
        ** возвращает DOM файла.
        */
        $dom_object->load_file($url);

        $anchors = $dom_object->find('a');

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
            
            if (!stristr($anchor->href, "/page/" . ($current_page + 1) . "/") === false) {
                $next_url = $anchor->href;
            }
        } 
        
        
    }
    
    return $suitable_links;
}

$url = $_POST['url_adress'];


if (isset($url)){

	$url = format_url($url);
    
    /*
    ** Подключаем библиотеку Simple HTML DOM,
    ** создаем объект.
    */
    include('./lib/simple_html_dom.php');
    
    $html = new simple_html_dom();
    /*
    ** --------------------
    */
    
    $pages_count = 20;
    
    $suitable_links = search_anchors($html, $url, $pages_count);
    
    $suitable_links = json_encode($suitable_links);
    
    echo $suitable_links;
    
    exit;
    
}
else {
	echo "Ошибка! Введите URL.";
    
    exit;
}
?>
