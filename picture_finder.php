<?PHP

	/**
	* Picture Finder
	* @package Picture_finder
	* @version 0.92
	* @author Triton project, University of Oxford
	*/

 
	/**
	* Use admin.php as basis for functions needed to create post thumbnail
	*/

	include "../../../wp-admin/admin.php";

	$home_url = get_bloginfo("siteurl");  	
	
	/**
	* Second page of code for the iframe to deal with choosing the picture to use
	*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
			<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US"> 
			<head> 
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
			<title>Politics in Spires &rsaquo; Uploads &#8212; WordPress</title> 
			<script type="text/javascript"> 
			//<![CDATA[
			addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
			var userSettings = {'url':'','uid':'<?PHP
		 
					/**
					* Set up the ajax for "set the featured image" code
					*/
				
					get_currentuserinfo();
					echo $current_user->user_ID;
				
				?>','time':'<?PHP echo time(); ?>'};
			var ajaxurl = '<?PHP echo $home_url; ?>/wp-admin/admin-ajax.php', pagenow = 'media-upload-popup', adminpage = 'media-upload-popup';
			//]]>
			</script> 
			<link rel='stylesheet' href='<?PHP echo $home_url; ?>/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=global,wp-admin,media&amp;ver=a5819c5a976216f1c44e5e55418aee0b' type='text/css' media='all' /> 
			<link rel='stylesheet' id='imgareaselect-css'  href='<?PHP echo $home_url; ?>/wp-includes/js/imgareaselect/imgareaselect.css?ver=0.9.1' type='text/css' media='all' /> 
			<link rel='stylesheet' id='colors-css'  href='<?PHP echo $home_url; ?>/wp-admin/css/colors-fresh.css?ver=20100610' type='text/css' media='all' /> 
			<!--[if lte IE 7]>
			<link rel='stylesheet' id='ie-css'  href='<?PHP echo $home_url; ?>/wp-admin/css/ie.css?ver=20100610' type='text/css' media='all' />
			<![endif]-->			
			<script type='text/javascript' src='<?PHP echo $home_url; ?>/wp-admin/load-scripts.php?c=1&amp;load=jquery,utils,swfupload-all,swfupload-handlers,json2&amp;ver=733749dfc00359ca23b46b29e2f304d2'></script> 
			</head> 
			<body id="media-upload"> <?PHP
						 
			/**
			* Create the FlickR search terms and prepare the post_data for reusing for the upload page
			*/	
			
			if ( !empty($_POST) && check_admin_referer('picture_finder_nonce','picture_finder_nonce_form_name') ){
				
							
				$upload = $_POST['upload'];
				$upload_url = $_POST['upload_url'];
				$post_edited = $_POST['post_edited'];
				$short_dir = $_POST['short_dir'];
				$search_term = $_POST['search_term'];
				
				/**
				* FlickR API variables
				*/
				
				$params = array(
						
					'per_page'	=> 500,
					'safe_search'	=> 1,
					'privacy_filter'	=> 1,
					'license'		=> '4%2C7',
					'text'		=> $search_term,
					'api_key'	=> '96990460a0675f30f3f7d4205672dce3',
					'method'	=> 'flickr.photos.search',
					'format'	=> 'php_serial'
				);

				$encoded_params = array();

				foreach ($params as $k => $v){

					$encoded_params[] = urlencode($k).'='.urlencode($v);
					
				}
				
				/**
				* Go to FlickR to get pictures
				*/

				$url = "http://api.flickr.com/services/rest/?".implode('&', $encoded_params);

				$rsp = file_get_contents($url);
				
				$rsp_obj = unserialize($rsp);
					
				$pictures = $rsp_obj['photos']['photo'];
				
				?>
					<h3 class="media-title" style="padding:10px 0 0 10px; margin:0px;">
								Triton - Picture search and attribution
					</h3>
				<?PHP
			
					$nonce = wp_create_nonce('picture-picker-nonce');
			
					while($pic = array_shift($pictures)){
					
						/**
						* Create and display the photo thumbnails as a lightbox so as to facilitate the choice
						*/
					
						echo "<div style=\"float:left; position:relative; width:140px; height:160px; background-color:#eee; border:1px solid #aaa; margin:10px\">";
						echo "<form action=\"picture_copy.php?_wpnonce=" . $nonce . "\" style=\"width:120px; margin:0 auto; text-align:center;\" method=\"POST\">";		
						echo "<img style=\"margin-top:15px\" src=\"http://farm" . $pic['farm'] . ".static.flickr.com/" . $pic['server'] . "/" . $pic['id'] . "_" . $pic['secret'] . "_t.jpg\" /><br />";
						echo "<input name=\"pic_data\" type=\"hidden\" value=\"" . urlencode(serialize($pic)) . "\" />";
						echo "<input type=\"hidden\" name=\"upload\" value=\"" . $upload . "\" />";
						echo "<input type=\"hidden\" name=\"upload_url\" value=\"" . $upload_url . "\" />";
						echo "<input type=\"hidden\" name=\"post_edited\" value=\"" . $post_edited . "\" />";
						echo "<input type=\"hidden\" name=\"short_dir\" value=\"" . $short_dir . "\" />";
						echo "<input type=\"hidden\" name=\"flickr_id\" value=\"" . $pic['id'] . "\" />";
						echo "<input type=\"submit\" value=\"choose\" style=\"margin-top:20px\" />";
						echo "</form></div>";	
					
					}

					/**
					* Allow for another search
					*/

				?><form class="media-upload-form type-form validate" action="" method="post">
						<p style="clear:left;">
							Search again
						</p>
						<input type="text" size="120" name="search_term" value='<?PHP echo stripcslashes($search_term); ?>' />
						<?PHP
								
							wp_nonce_field('picture_finder_nonce','picture_finder_nonce_form_name');
				
						?>
						<input type="hidden" name="upload" value="<?PHP echo $upload; ?>" />
						<input type="hidden" name="upload_url" value="<?PHP echo $upload_url; ?>" />
						<input type="hidden" name="post_edited" value="<?PHP echo $post_edited; ?>" />
						<input type="hidden" name="short_dir" value="<?PHP echo $short_dir; ?>" />
						<input type="submit" class="button" value="Search for pictures" />
					</form><?PHP
					
			}
			
?>