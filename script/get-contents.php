<?php
include_once('simple_html_dom.php');

function scraping_digg($page=1) {
    // create HTML DOM
    // $html = file_get_html('http://digg.com/');

    ini_set("memory_limit",-1);
   // $url='https://www.oneillhomes.ca/search/results/?page='.$page;
   
    $url='https://www.oneillhomes.ca/search/results/?city=North+Vancouver&page='.$page;
   
   
    $path= dirname(__FILE__).'/../data/page-contents/'.$page.'.html';
    //$path= $page.'html';
    
    $html = @file_get_html($url);
   
    @file_put_contents($path,$html);
   
   return (strlen($html) > 18000)?1:0;
}




function data(){
    $item_defaults=[
        'id'=>'',
        'title'=>'',
        'url'=>'',
        'image'=>'',
        'address1'=>'',
        'owner'=>'',
        'description'=>'',
        'beds'=>'',
        'baths'=>'',
        'sqft'=>'',
        'price'=>'',
        'city'=>'',
        'property_type'=>'',
    ];

   # print($html2);
    //die($html);
    // get news block
   // foreach($html->find('div.news-summary') as $article) {
    foreach($html2->find('div.results') as $article) {

      // file_put_contents('datasingle.html',$article);die;
        //print_r($article);die;
        // get title
        $item['id'] = trim($article->find('.number', 0)->plaintext);
        $item['address'] = trim($article->find('.address', 0)->plaintext);
        $address = explode(',',$item['address']);
        $item['city'] = end(@$address);
        $item['address1'] = (@$address[0]);
        $item['owner'] = str_replace(["Courtesy of"],"",trim($article->find('.courtesy', 0)->plaintext));
        
        //TODO
        $item['title'] = trim($article->find('.address', 0)->plaintext);
        
        
        $item['description'] = trim($article->find('.property-description', 0)->plaintext);
        $item['price'] = str_replace(["$",","],"",trim($article->find('.price', 0)->plaintext));
        // get details
        $item['url'] = 'https://www.oneillhomes.ca'.trim($article->find('.property-thumb a', 0)->href);
        // get intro
        $item['image'] = trim($article->find('.property-thumb img', 0)->src);
        $features = $article->find('.featured-details ul li');

        if(!empty($features)){
             foreach($features as $li) {

                 $key = trim($li->find('.detail-title', 0)->plaintext);
                 $count = trim($li->find('.number', 0)->plaintext);

                 $item[strtolower($key)] = trim($count);
             }
        }

            if(isset($item['mls&reg; #'])){
                $item['id'] = $item['mls&reg; #'];
                unset($item['mls&reg; #']);
            }
             if(isset($item['sqft.'])){
                $item['sqft'] = str_replace(["$",","],"",$item['sqft.']);
                unset($item['sqft.']);
            }

        $ret[] = array_merge($item_defaults,$item);
        print_r($ret);die;
    }
    
    // clean up memory
    $html2->clear();
    unset($html);

    return $ret;
}


// -----------------------------------------------------------------------------
// test it!

// "http://digg.com" will check user_agent header...
ini_set('user_agent', 'My-Application/2.5');


$page=100;

$i=1;

while($i < $page ){

   
    $ret = scraping_digg($i);

     if(!$ret){
         print("*********   Failed for $i   *******");die;
         file_put_contents('failed_page.txt',$i,FILE_APPEND);
     }
    print " processed page = $i  ";
    $i++;
}


?>