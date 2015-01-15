<?php 
/*
	Plugin Name: Per Post Code Safe
	Description: Enables custom styles, scripts, and a save place for post content
	Author: forlogos
	Author URI: http://j3webworks.com
	Requires at least: 3.3
	Version: 1.0
*/


//start flgs_ppcs meta box
$flgs_ppcs_mb_key="flgs_ppcs_mbox";
function flgs_ppcs_addbox() {
     add_meta_box('flgs_ppcs','Code Safe','display_flgs_ppcs_mb','page','normal','high');
}
add_action('admin_menu','flgs_ppcs_addbox');
$flgs_ppcs_mbs=array(
	"css" => array(
		"name" => "css",
		"title" => "Custom CSS",
		"description" => "Custom CSS for this page only."),
	"js" => array(
		"name" => "js",
		"title" => "Custom JS",
		"description" => "Custom JS for this page only."),
	"html_bak" => array(
	"name" => "html_bak",
	"title" => "HTML Content Backup",
	"description" => "A nice save place for backing up main content."),
);
function display_flgs_ppcs_mb() {
     global $post,$flgs_ppcs_mbs,$flgs_ppcs_mb_key; ?>
	 <script type="text/javascript">jQuery(document).ready(function($){
$(".show_hide").click(function() {
	$(this).next().slideToggle("slow");
});
});</script>
	<style type="text/css">
.form-field input.ed_button {width:auto;}
	</style>
     <div class="form-wrap">
          <?php wp_nonce_field(plugin_basename(__FILE__),$flgs_ppcs_mb_key. '_wpnonce',false,true);
          foreach($flgs_ppcs_mbs as $meta_box) {
               $data=get_post_meta($post->ID,$flgs_ppcs_mb_key,true);?>
               <div class="form-field form-required">
                    <label for="<?php echo $meta_box[ 'name' ]; ?>"<?php if($meta_box[ 'name'] == 'css' || $meta_box[ 'name'] == 'js' || $meta_box[ 'name'] == 'html_bak') {echo ' class="show_hide"';} ?>><?php echo $meta_box[ 'title' ]; ?></label>
<?php if($meta_box[ 'name'] == 'css' || $meta_box[ 'name'] == 'js' || $meta_box['name'] == 'html_bak') {
	echo '<textarea name="'.$meta_box['name'].'" tabindex="1" rows="20"'.($meta_box[ 'name'] == 'css' || $meta_box[ 'name'] == 'js' || $meta_box['name'] == 'html_bak'? ' style="display:none;"' : '').'>'.htmlspecialchars($data[$meta_box['name']]).'</textarea>';
}else{
     echo '<input type="text" name="'.$meta_box['name'].'" value="'.htmlspecialchars($data[$meta_box['name']]).'"/>';
} ?>
                    <p><?php echo $meta_box['description']; ?></p>
               </div>
          <?php } ?>
     </div>
<?php }
function save_flgs_ppcs_mb($post_id) {
     global $post,$flgs_ppcs_mbs,$flgs_ppcs_mb_key;
     foreach($flgs_ppcs_mbs as $meta_box) {
          if(isset($_REQUEST[$meta_box['name']])) {
               $data[$meta_box['name']]=$_POST[$meta_box['name']];
          }
     }
     if(isset($_POST[$flgs_ppcs_mb_key.'_wpnonce'])) {
          if (!wp_verify_nonce($_POST[$flgs_ppcs_mb_key. '_wpnonce'],plugin_basename(__FILE__)))
          return $post_id;
     }
     if(!current_user_can('edit_post',$post_id))
          return $post_id;
     if(!empty($data)) {
          update_post_meta($post_id,$flgs_ppcs_mb_key,$data);
     }
}
add_action('save_post','save_flgs_ppcs_mb');
//end flgs_ppcs meta box

// Add scripts to wp_head()
function flgs_ppcs_wphead() {
	if(is_singular()) {	
		$flgs_data = get_post_meta(get_the_ID(),'flgs_ppcs_mbox',true);
		echo ($flgs_data['css']!='' ? '<style type="text/css">'.$flgs_data['css'].'</style>' : '<meta name="jjj" content="blcnk css" />').($flgs_data['js']!='' ? '<script type="text/javascript">'.$flgs_data['js'].'</script>' : '<meta name="jjj" content="balnk js" />');
	}
}
add_action('wp_head','flgs_ppcs_wphead');
?>