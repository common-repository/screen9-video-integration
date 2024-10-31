<?php
function listCategories($current){
	$filter = array();
	$arguments = array(
			$filter, 100, 0
	);
	$cats = screen9_call("listCategories", $arguments);
	$i=0;	
	foreach ($cats as $cat) {
		$selected="";
		if($current==$cat['categoryid']||($current=="" && $i==0)){
			$selected = "selected";
		}
		echo(sprintf("<option value='%s' %s>%s</option>", $cat['categoryid'], $selected, $cat['categoryname']));
		$i++;
	}
}

function listThumbnails($thumbs, $selected, $titleimages){
	$i=0;	
	if($thumbs){
		foreach ($thumbs as $thumb) {
			if($thumb==$selected||($selected == "" && $i==0)){
				$checked=" checked";
			}else{
				$checked="";
			}
			echo(sprintf("<span style='float: left;text-align: center;'><input type='radio' name='screen9_image' value='%s,%s' %s><br><img src='%s'/></span>",$thumb,$titleimages[$i],$checked,$thumb));
			$i++;
		}
	}	
}

function setPubDate($videoid){
	date_default_timezone_set(get_option('timezone_string'));
	$date = mktime($_POST['hh'], $_POST['mn'], $_POST['ss'], $_POST['mm'], $_POST['jj'], $_POST['aa'], -1);
	$datetime = new DateTime();
	$datetime->setTimestamp($date);
	$timezone = new DateTimeZone("UTC");
	$datetime->setTimezone($timezone);
	$pubdate = date_format($datetime,"Y-m-d H:i:s");
	
	$pubdate_args = array(
		$videoid, "picsearch.publishfrom", $pubdate
	);
	screen9_call("setProperty", $pubdate_args);	
}
?>