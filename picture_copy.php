<?PHP

	/**
	* Picture Finder
	* @package Picture_finder
	* @version 1.0
	* @author Triton project, University of Oxford
	*/

 
	/**
	* Use admin.php as basis for functions needed to create post thumbnail
	*/

		include "../../../wp-admin/admin.php";
		
		$home_url = get_bloginfo("siteurl"); 
		
		$nonce=$_REQUEST['_wpnonce'];
						
		if(!wp_verify_nonce($nonce, "picture-picker-nonce")){
		
			die();
		
		}
		
		if($_SERVER['HTTP_REFERER']!== $home_url . "/wp-content/plugins/flickr-picture-find-and-attribute/picture_finder.php"){
		
			die();
		
		}
			
	/**
	* Third page of the iframe
	*/
	
?><html>
	<head>
		
		<link rel='stylesheet' href='<?PHP echo $home_url; ?>/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=global,wp-admin,media&amp;ver=a5819c5a976216f1c44e5e55418aee0b' type='text/css' media='all' /> 
			<link rel='stylesheet' id='imgareaselect-css'  href='<?PHP echo $home_url; ?>/wp-includes/js/imgareaselect/imgareaselect.css?ver=0.9.1' type='text/css' media='all' /> 
			<link rel='stylesheet' id='colors-css'  href='<?PHP echo $home_url; ?>/wp-admin/css/colors-fresh.css?ver=20100610' type='text/css' media='all' /> 
			
			<link rel='stylesheet' id='ie-css'  href='<?PHP echo $home_url; ?>/wp-admin/css/ie.css?ver=20100610' type='text/css' media='all' />
					
			<script type="text/javascript"> 
				//<![CDATA[
				addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
								
				var userSettings = {'url':'','uid':'<?PHP
				
					/**
					* AJAX to handle the thumbnail image creation
					*/
				
					get_currentuserinfo();
					echo $current_user->user_ID;
				
				?>','time':'<?PHP
				
					echo time();
				
				?>'};
				var ajaxurl = '<?PHP echo get_bloginfo("siteurl"); ?>/wp-admin/admin-ajax.php', pagenow = 'media-upload-popup', adminpage = 'media-upload-popup';
				//]]>
			</script> 
		
		
		<script type='text/javascript' src='<?PHP echo $home_url; ?>/wp-admin/load-scripts.php?c=1&amp;load=ajax,set-post-thumbnail,jquery,utils,swfupload-all,swfupload-handlers,json2,jquery-ui-core,jquery-ui-sortable,admin-gallery&amp;ver=74c390eaa960a9c93c7a2655d30e9ffe'>
		</script>
		<script type="text/javascript" language="JavaScript">
			
			var setPostThumbnailL10n = {
				setThumbnail: "Use as featured image",
				saving: "Saving...",
				error: "Could not set that as the thumbnail image. Try a different attachment.",
				done: "Done"
			};

				
			function insert_picture(url, image_id){
				
				string = '<a target="_blank" href="' + url + '"><img src="' + url + '" /></a>';
				
				var win = window.dialogArguments || opener || parent || top;
				
				win.send_to_editor(string);
      				      				
      			return true;
								
			}
						
		</script>		
		
	</head>	
	<body>		
		<h3 class="media-title" style="padding:10px; margin:0px;">
					Triton - Picture search and attribution
		</h3>
		<div style="margin:10px">
	<?PHP
	
	// Works in single post outside of the Loop
	
	/**
	* Add post id to the javascript for the page
	*/
	
	echo '<script type="text/javascript">post_id=' . $_POST['post_edited']  . ';</script>';
	
	/**
	* Create a new FlickR api request for the selected picture's author
	*/
	
	$params = array(
			
		'photo_id'	=> $_POST['flickr_id'],		
		'api_key'	=> '728ee0bc70c3fc45c03790b209889847',
		'method'	=> 'flickr.photos.getInfo',
		'format'	=> 'php_serial'
	);
	
	
	$encoded_params = array();

	foreach ($params as $k => $v){

		$encoded_params[] = urlencode($k).'='.urlencode($v);
		
	}
	
	/**
	* call the API and decode the response
	*/

	$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);

	$rsp = file_get_contents($url);
	
	$rsp_obj = unserialize($rsp);	
	
	$pic_data = urldecode($_POST['pic_data']);
	
	$pic = unserialize($pic_data);
	
	if($rsp_obj['photo']['owner']['realname']==""){
	
		$author = $rsp_obj['photo']['owner']['username'];
	
	}else{
	
		$author = $rsp_obj['photo']['owner']['realname'];
	
	}
	
	if($author==""){
	
		$author = $rsp_obj['photo']['notes']['note']['authorname'];
	
	}
		
	$attrib_url = "farm" . $pic['farm'] . ".static.flickr.com/" . $pic['server'] . "/" . $pic['id'] . "_" . $pic['secret'] . ".jpg";

	/**
	* Get the picture from Flickr for processing
	*/
			
	$img = file_get_contents("https://farm" . $pic['farm'] . ".static.flickr.com/" . $pic['server'] . "/" . $pic['id'] . "_" . $pic['secret'] . ".jpg");
	
	file_put_contents($_POST['upload'] . "/" . $pic['id'] . "_" . $pic['secret'] . ".jpg", $img);
	
	/**
	* Get the picture stats
	*/
		
	$image_info = getimagesize($_POST['upload'] . "/" . $pic['id'] . "_" . $pic['secret'] . ".jpg");	
			
	$attrib_image = imagecreatefromjpeg($_POST['upload'] . "/" . $pic['id'] . "_" . $pic['secret'] . ".jpg");
	
	/**
	* Use GD to make the picture
	*/
	
	$final_image = imagecreatetruecolor($image_info[0],$image_info[1]+50);
	
	/**
	* Make the picture and add the attribution text
	*/
	
	imagecopyresized ($final_image, $attrib_image, 0, 0, 0, 0, $image_info[0],$image_info[1], $image_info[0],$image_info[1]);
	
	imagettftext ($final_image , 10, 0, 2, $image_info[1]+15, imagecolorallocate($final_image,255,255,255), "ARIAL.TTF", $attrib_url);
	
	imagettftext ($final_image , 10, 0, 2, $image_info[1]+35, imagecolorallocate($final_image,255,255,255), "ARIAL.TTF", $author);
				
	imagejpeg($final_image, $_POST['upload'] . "/" . $pic['id'] . "_" . $pic['secret'] . "_attrib.jpg");
	
	/**
	* Show the picture to the user as part of the iframe
	*/
			
	echo "<img src=\"" . $_POST['upload_url'] . "/" . $pic['id'] . "_" . $pic['secret'] . "_attrib.jpg\" />";		
					
	$image_url = $_POST['upload_url'] . "/" . $pic['id'] . "_" . $pic['secret'] . "_attrib.jpg";				
					
	$data = array (
	
		"post_author" => get_current_user_id(), 	
		"post_date" => date("Y-m-d G:i:s",time()),
		"post_date_gmt" => gmdate("Y-m-d G:i:s",time()),	
		"post_name" => $pic['title'], 
		"post_title" => $pic['title'],
		"post_type"	=> "attachment",	
		"post_mime_type" =>	"image/jpeg",
		"post_status" => "inherit",
		"post_parent" => $_POST['post_edited'],
		"guid" => $_POST['upload_url'] . "/" . $pic['id'] . "_" . $pic['secret'] . "_attrib.jpg"
		
	);
	
	/**
	* Add picture as associated to this post (for the gallery and so on)
	*/
						
	$wpdb->insert("wp_posts",$data);
		
	$picture_post_id = $wpdb->insert_id;
	
	update_post_meta($_POST['post_edited'], '_wp_attached_file', substr($_POST['short_dir'],1,strlen($_POST['short_dir'])-1) . "/" . $pic['id'] . "_" . $pic['secret'] . "_attrib.jpg");
	
	$filepath = $_POST['upload'] . "/" . $pic['id'] . "_" . $pic['secret'] . "_attrib.jpg";
	
	$attach_data = wp_generate_attachment_metadata( $wpdb->insert_id, $filepath );
	
	wp_update_attachment_metadata( $wpdb->insert_id, $attach_data );
	
	update_post_meta($_POST['post_edited'], "_thumbnail_id", $wpdb->insert_id);
	
	$post_id = $_POST['post_edited'];
	
	$image_details = getimagesize($_POST['upload'] . "/" . $pic['id'] . "_" . $pic['secret'] . "_attrib.jpg");
	
	
?> </div>
	<div style="clear:left; margin:10px">
		<p>				
			<button class="button" onclick="insert_picture('<?PHP echo $image_url; ?>','<?PHP echo $wpdb->insert_id; ?>')"> Insert into Post</button> 
		</p> 	 
	</div>
</body>
</html>