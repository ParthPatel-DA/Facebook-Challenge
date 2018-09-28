<?php

    function getAllImages($albumID, $fb){
        $images = "";
        $accessToken = $_SESSION['access_token'];
        // get 100 images data
        $re = $fb->get('/'.$albumID.'?fields=name,photos.limit(100){images}',$accessToken);
        $graphEdge = $re->getGraphNode();
        // generate string of image source separated by "~"
        for($j=0;$j<count($graphEdge['photos']);$j++){
            $images .= $graphEdge['photos'][$j]['images'][0]['source']."~";
        }
        $a = $re->getDecodedBody();
        // get next paging url
        $str = $a['photos']['paging']['next'];
        if($str!=""){
            $arr = explode("v3.1",$str);
            // get next images
            $re = $fb->get($arr[1],$accessToken);
            $graphEdge = $re->getGraphEdge();
            $images1 = json_decode($graphEdge, true);
            foreach($images1 as $img){
                $images .=$img['images'][0]['source']."~";
            }
        }
        return $images; // return all images (upto 200)
    }