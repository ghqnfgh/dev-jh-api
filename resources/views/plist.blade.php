<html>
<head>
</head>
<body>
<?php

include_once('/var/www/html/Minikurly/resources/views/simple_html_dom.php');
$html = file_get_html('http://www.kurly.com/shop/goods/goods_list.php?category=001&page=1');
//echo $html;
header("Content-Type: text/html; charset=euc-kr");
// 모든 이미지태그를 찾아냅니다.
foreach($html->find('div') as $element) 
       echo $element. '<br>';                                  

// 모든 a태그를 찾아내어 href속성을 뿌려줍니다.
//foreach($html->find('a') as $element)                             
 //      echo $element->href . '<br>';

// 컨텐츠내에 텍스트들만 가져옵니다.
//echo file_get_html('http://www.google.com/')->plaintext; 

// css jquery 많이 만져보셨다면 선택자는 거의 동일합니다.
//몇번째 a태그 같은 경우도 가져올수 있구요
/*foreach($html->find('div.article') as $article) {
    $item['title']     = $article->find('div.title', 0)->plaintext;
    $item['intro']    = $article->find('div.intro', 0)->plaintext;
    $item['details'] = $article->find('div.details', 0)->plaintext;
    $articles[] = $item;
}*/

//가져오기전 해당 태그내의 텍스트도 이렇게 간단히 변경가능합니다.
?>
</body>
