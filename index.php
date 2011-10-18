<?PHP

	/*
	Plugin Name: Picture finder
	Plugin URI: http://openattribute.com
	Description: Adds the ability to search for Flickr content, bring it bak into WordPress and attribute it.
	Version: 0.9
	Author: Pat Lockley
	Author URI: http://politicsinspires.org
	*/
	
	/**
	* Picture Finder
	* @package Picture_finder
	* @version 1.0
	* @author Triton project, University of Oxford
	*/

 
	/**
	* add_picture_finder_action - creates the Iframe for the picture picker
	* @version 1.0
	* @author Triton project, University of Oxford
	*/
	function add_picture_finder_action() {
	
		/**
		* Configure some variables and access some details before rendering the frame
		*/
  
  		$url = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
  
  		$details = wp_upload_dir();
  		
  		$home_url = get_bloginfo("siteurl");
  		  		
  		$path = $details['path'];
  		
  		$url_of_site = $details['url'];
  		
  		$short_dir = $details['subdir'];
		
		/**
		* Render the frame out
		*/
  		
  		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
			<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US"> 
			<head> 
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 			
			<link rel='stylesheet' href='<?PHP echo $home_url; ?>/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=global,wp-admin,media&amp;ver=a5819c5a976216f1c44e5e55418aee0b' type='text/css' media='all' /> 
			<link rel='stylesheet' id='imgareaselect-css'  href='<?PHP echo $home_url; ?>/wp-includes/js/imgareaselect/imgareaselect.css?ver=0.9.1' type='text/css' media='all' /> 
			<link rel='stylesheet' id='colors-css'  href='<?PHP echo $home_url; ?>/wp-admin/css/colors-fresh.css?ver=20100610' type='text/css' media='all' /> 
			<!--[if lte IE 7]>
			<link rel='stylesheet' id='ie-css'  href='<?PHP echo $home_url; ?>/wp-admin/css/ie.css?ver=20100610' type='text/css' media='all' />
			<![endif]-->			
			<script type='text/javascript' src='<?PHP echo $home_url; ?>/wp-admin/load-scripts.php?c=1&amp;load=jquery,utils,swfupload-all,swfupload-handlers,json2&amp;ver=733749dfc00359ca23b46b29e2f304d2'></script> 
			</head> 
			<body id="media-upload"> 
			<form class="media-upload-form type-form validate" action="<?PHP echo $url; ?>picture_finder.php" method="POST">
				<h3 class="media-title">
					Triton - Picture search and attribution
				</h3>
				<p>
					Enter a search term in the box below and then click on 'search for pictures'
				</p>
				<?PHP
								
					wp_nonce_field('picture_finder_nonce','picture_finder_nonce_form_name');
				
				?>
				<input type="text" size="120" name="search_term" value="Enter search terms here" />
				<input type="hidden" name="upload" value="<?PHP echo $path; ?>" />
				<input type="hidden" name="upload_url" value="<?PHP echo $url_of_site; ?>" />
				<input type="hidden" name="post_edited" value="<?PHP echo $_GET['post_id']; ?>" />
				<input type="hidden" name="short_dir" value="<?PHP echo $short_dir; ?>" />
				<input type="submit" class="button" value="Search for pictures" />
			</form></body></html>
		
		<?php
	
	}

	/**
	* picture_finder_button - adds the picture finder button to the space above the post window
	* @version 1.0
	* @author Triton project, University of Oxford
	*/
	function picture_finder_button($context) {
	
		global $post_ID;
  
    	$icon_url = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'triton.png';
    
    	$string = get_permalink();
    
    	$result = '<a href="media-upload.php?&amp;post_id=' . $post_ID . '&amp;type=picture_finder&amp;TB_iframe=1" class="thickbox" title="' . __('Triton Picture Search and Attribution') . '"><img src="'.$icon_url.'" alt="'. __('Find and attribute pictures') .'" title="'. __('Find and attribute pictures') .'" /></a>';
       
    	return $context . $result;
    	
	}

	add_filter('media_buttons_context', 'picture_finder_button');
	add_filter('media_upload_picture_finder', 'add_picture_finder_action');

?>