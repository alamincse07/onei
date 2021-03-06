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
   
    global $city;
    ini_set("memory_limit",-1);
   
    $val=[];
    
    $html = @file_get_html($url);
     $html2 = str_get_html($html);
     
    $path= dirname(__FILE__).'/../data/'.str_replace(" ","-",$city).'/details-page-contents/'.$id.'.html';
    @file_put_contents($path,$html);
    
     if(strlen($html) < 300){ return [];}
    if(! $html2->find('.prop-address')){return [];}
      $val['address'] = trim(@$html2->find('.prop-address', 0)->plaintext);
    foreach($html2->find('.property_detail_specs') as $spec) {

        if( trim($spec->find('dt', 0)->plaintext) == 'Dwelling Type'){

            $val['property_type'] = trim($spec->find('dd', 0)->plaintext);
           
         //   break;
        }
        if( trim($spec->find('dt', 0)->plaintext) == 'Area'){

            $val['area'] = trim($spec->find('dd', 0)->plaintext);
           
          // break;
        }

        if( trim($spec->find('dt', 0)->plaintext) == 'Community'){

            $val['community'] = trim($spec->find('dd', 0)->plaintext);
           
           break;
        }
        

    }
 $html2->clear();
    unset($html2);
    unset($html);
    return $val;

   
}




function GetAndPrepareData($file_no){

        global $city;
     $file = dirname(__FILE__).'/../data/'.str_replace(" ","-",$city).'/page-contents/'.$file_no.'.html';
    
     if(file_exists($file)){}else{ return 0;}

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
        'area'=>'',
        'community'=>'',
    ];

    $ret=[];
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
     
        preg_match("/[0-9]+/",  $item['price'] ,$match);
        $item['price']=$match[0];
        # print($item['price'].'--');
        // get details
         $item['url'] = 'https://www.oneillhomes.ca'.trim($article->find('.property-thumb a', 0)->href).'?origin=campaign';
        // get intro
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

             if(!empty($item['id'])){

                $item['url'] = 'https://www.oneillhomes.ca/property/'.$item['id'].'/?medium=dynamic_campaign_feed';
     
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
   # print_r($ret);die;


   if(count($ret)>0){
    file_put_contents(dirname(__FILE__).'/../data/'.str_replace(" ","-",$city).'/properties/'.$file_no.'.json',json_encode($ret));
    
   }
   unset( $ret);
    return 1;
}


// -----------------------------------------------------------------------------
// test it!

// "http://digg.com" will check user_agent header...
ini_set('user_agent', 'My-Application/2.5');


$page=100;

$i=1;

print " \nProcess listing data collection for $city\n";

while($i <= $page ){

   
    $ret = GetAndPrepareData($i);

     if(!$ret){
         file_put_contents('failed_page.txt',$i,FILE_APPEND);die;
             }
    print " processed page = $i  ";
    $i++;
}



?>