<?php
include_once('simple_html_dom.php');


$system_args = getopt('c:');
if(isset($system_args['c'])) {
    $city = $system_args['c'];
} else {
    print("Missing city name `c`\n");
    die;
}

function scraping_page($url='',$id='') {

    ini_set("memory_limit",-1);
   
     $path= dirname(__FILE__).'/../data/'. $city.'/details-page-contents/'.$id.'.html';
    //$path= $page.'html';
    
    $html = @file_get_html($url);
    @file_put_contents($path,$html);
    

   
}




function GetAndPrepareData($file_no){

     $file= dirname(__FILE__).'/../data/'. $city.'/page-contents/'.$file_no.'.html';
    
    $html= file_get_contents($file);
    $html2 = str_get_html($html);

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
        'short_info'=>'',
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
        if(!empty($address)){
        $item['city'] = end($address);
        $item['address1'] = ($address[0]);
        }
        
        $item['owner'] = str_replace(["Courtesy of"],"",trim($article->find('.courtesy', 0)->plaintext));
        
        //TODO
        $item['title'] = trim($article->find('.address', 0)->plaintext);
        
        
        $item['description'] = trim($article->find('.property-description', 0)->plaintext);
        $item['price'] = str_replace(["$",","],"",trim($article->find('.price', 0)->plaintext));
        $item['price']  = preg_replace("/[^0-9.]/", "", $item['price'] );
        $item['url'] = 'https://www.oneillhomes.ca/property/'.$item['id'];  // get intro
        $item['image'] = trim($article->find('.property-thumb img', 0)->src);
        if(!strstr($item['image'],'http')){ $item['image'] = 'https://www.oneillhomes.ca'.$item['image'] ;}
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

            //get type again
           $property_info = scraping_page(@$item['url'], $item['id'] );
           $short_info= [];
           if(!empty($item['beds'])){ $short_info[] =  $item['beds'] . " beds";}
           if(!empty($item['baths'])){ $short_info[] =  $item['baths'] . " baths";}
           if(!empty($item['sqft'])){ $short_info[] =  $item['sqft'] . " sq ft";}
           $item['short_info']= implode(', ',$short_info);



        $ret[] = array_merge($item_defaults,$item,$property_info);
        unset($item);
       // print_r($ret);die;
    }
    
    // clean up memory
    $html2->clear();
    unset($html);
    #print_r($ret);die;


    file_put_contents(dirname(__FILE__).'/../data/'. $city.'/properties/'.$file_no.'.json',json_encode($ret));
    unset( $ret);
    return 1;
}


// -----------------------------------------------------------------------------
// test it!

// "http://digg.com" will check user_agent header...
ini_set('user_agent', 'My-Application/2.5');


$page=860;

$i=1;

while($i <= $page ){

   
    $ret = GetAndPrepareData($i);

     if(!$ret){
         file_put_contents('failed_page.txt',$i,FILE_APPEND);
     }
    print " processed page = $i  ";
    $i++;
}



?>