<?php
/*
Plugin Name: AppStore Italia
Plugin URI: http://www.belloweb.it/download/plugin-wordpress-appstore-italia/
Description: AppStore Italy is a plugin that allows you to include URL, price, photos, vote and application category in your page. You can add links to application AppStore using [app #id_application]
Version:1.1
Author: Giovanni
Author URI: http://www.belloweb.it


*/
if ( !defined('ABSPATH') ) {
	require_once ( '../../../wp-blog-header.php');
}

include ("definitions.php");

function AppStoreLinks_plugin_BelloWeb_callback($match) {
	$searchid = $match[1];
	return createLinkAppStore($searchid);
}

function createHTMLAppStore($linkImg, $nomeApp, $categoriaApp, $prezzoApp, $pesoApp, $linkAppStore, $votoApp, $prezzoOrig){

	$strHtml = "<div  style='border: solid 1px #cccccc;background-color: #f3f3f3; "
			."width:480px; height:150px;padding:0px; float:left;margin-top:10px; "
			."margin-bottom:30px; border-radius:8px 8px 8px 8px; '>"
				."<div style='float:left; width: 78px; margin:0px;text-align:center;padding-left:12px; "
				."padding-top:18px; '>"
          				."<img src='".$linkImg."' width='110' height='110' style='margin:0px; padding:0px;'/>"
    				."</div>"
				."<div style='background-color: #f3f3f3; float:left; width: 330px; height:124px; "
				."margin:5px 5px 5px; padding:6px; border: solid 1px #a2a2a2; border-radius:8px 8px 8px 8px; "
				."float:right; '>"
    				."<b>Nome: </b>".$nomeApp."<br />"
        			."<b>Categoria: </b>".$categoriaApp."<br/>"
        			."<b>Peso: </b>".$pesoApp."<br />"
        			."<b><font color='#666666'>";
        			if (!empty($prezzoOrig))
        				$strHtml = $strHtml."Prezzo originale: ".$prezzoOrig."</font>";
        			$strHtml = $strHtml."<br /><br /><b>&nbsp;&nbsp;&nbsp;VOTO: </b>".$votoApp
        			."<div style=' float:right; width:212px; height:81px; margin-bottom:50px; "
        			."background-color: none; margin-top:-75px;margin-left: 80px; '>"
        				."<div style='background:url(".plugins_url( 'price_app.png', __FILE__ )."
) no-repeat; margin-top:-20px; margin-left:70px; height:30px;'>"
        					."<div style='margin-left:40px;color: #FFF;line-height:30px; font-size:16px ' >"
        					."<b><font size='+1' >".$prezzoApp."</font></b>"
            					."</div>"
					."</div>"
            				."<div style='float:left;'><BR />"
            				."<a href='".$linkAppStore."'>"
                			."<img src='".plugins_url( 'download-app-store.png', __FILE__ )."' width='200px' height='60px'/ border='no'></a></b>"
            				."</div>"
        			."</div>"
				."</div>"
			."</div>"
		."<p><br style='clear:left;'>";

		return $strHtml;
}


function createLinkAppStore($idApp){
	list($keyApp, $prezzoOrig) = explode('|', $idApp);

	$obj = getContentApp($keyApp);
	if($obj->{'resultCount'} > 0){
		// prendo il primo risultato.. do per scontato che ce ne sia soltanto uno
		//function createHTMLAppStore($linkImg, $nomeApp, $categoriaApp, $prezzoApp,  $pesoApp, $linkAppStore, $votoApp)
		$linkImg = $obj->results[0]->artworkUrl100;
                if (empty($linkImg ))
			$linkImg = $obj->results[0]->artworkUrl60;
		$nomeApp = $obj->results[0]->trackName;
		$categoriaApp = $obj->results[0]->primaryGenreName;
		$prezzoApp = $obj->results[0]->price;
		$pesoApp = $obj->results[0]->fileSizeBytes;
		$linkAppStore = $obj->results[0]->trackViewUrl;
		$votoApp = $obj->results[0]->averageUserRating;

		if(!$prezzoApp >0)
			$prezzoApp = "Gratis";

		// trasformo il peso il kb
		$pesoApp = (ceil($pesoApp / 1024)) . "Kb";

		$linkAppStor2 = $linkAppStore;

		$strHtml = createHTMLAppStore($linkImg, $nomeApp, $categoriaApp, $prezzoApp,  $pesoApp, $linkAppStor2, $votoApp, $prezzoOrig);

		return $strHtml;
	}
}

function getContentApp($idApp){

	$searchlink = APPSTORESEARCHLINK.$idApp;

	$result = @file_get_contents($searchlink);

	// Decode Content
	$obj = json_decode($result);

	return $obj;
}

function AppStoreLinks_plugin_BelloWeb($content)
{
	return (preg_replace_callback(APPSTORELINKS_REGEXP, 'AppStoreLinks_plugin_BelloWeb_callback', $content));
}




add_filter('the_content', 'AppStoreLinks_plugin_BelloWeb');
?>