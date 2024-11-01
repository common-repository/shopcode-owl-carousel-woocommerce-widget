<?php
/**
Plugin Name: ShopCode Owl Carousel WooCommerce Widget
Plugin URI: shopcode
Description: ShopCode show product WooCommerce owl carousel on categories responsive 
Author: Shopcode
Version: 1.0
Author URI: shopcode.org
*/



#prefix: ocww
#function main: owl_carousel_woocommerce_widget  
#CLASS Ocww_Owl_Carousel_Woocommerce_Widget
#CLASSNAME  ocww-owl-carousel-woocommerce-widget  




if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/**
 * Define Path 
*/
define( 'SHOPCODE_OCWW_PLUGIN_URI', plugin_dir_url( __FILE__ ));

// include thumb auto resize
require_once('BFI_Thumb.php');
	
// include  font-awesome from https://fontawesome.com/v4.7.0/assets/font-awesome-4.7.0.zip
add_action('wp_enqueue_scripts', 'ocww_include_font_awesome');
 function ocww_include_font_awesome() {
	
	wp_register_style( 'ocww-include-font-wesome',  SHOPCODE_OCWW_PLUGIN_URI.'font-awesome/css/font-awesome.min.css', '', '4.7.0', false );
	wp_enqueue_style( 'ocww-include-font-wesome' );

}
/**
 * Adding scripts
 */
 add_action( 'wp_enqueue_scripts', 'shopcode_ocww_adding_scripts' ); 	
 if( !function_exists('shopcode_ocww_adding_scripts') ){
	function shopcode_ocww_adding_scripts() {
	
	
		// include carousel ver 2.2.1
		wp_register_script( 'ocww_owl_carousel', SHOPCODE_OCWW_PLUGIN_URI.'assets/js/owl.carousel.min.js', array('jquery'), '2.2.1', false );
		wp_enqueue_script( 'ocww_owl_carousel' );
		
		
		wp_register_style( 'ocww_owl_carousel_style', SHOPCODE_OCWW_PLUGIN_URI.'assets/css/owl.carousel.min.css', '', '2.2.1', false );
		wp_enqueue_style( 'ocww_owl_carousel_style' );
		
		// include main_css
		wp_register_style( 'ocww_owl_main_style', SHOPCODE_OCWW_PLUGIN_URI.'assets/css/main.css', '', '1.0', false );
		wp_enqueue_style( 'ocww_owl_main_style' );

		// include main_js
		wp_enqueue_script( 'ocww_main_scripts', SHOPCODE_OCWW_PLUGIN_URI.('assets/js/main.js'), array( 'ocww_owl_carousel' ), '1.0', true );
		
		
	}}
/**

 * CLASS WIDGET

 */
class Ocww_Owl_Carousel_Woocommerce_Widget extends WP_Widget {
	
	//	@var string (The plugin version)		
	var $version = '1.0';
	//	@var string $localizationDomain (Domain used for localization)
	var $localizationDomain = 'occw-owl-carousel-woocommerce-widget';
	//	@var string $pluginurl (The url to this plugin)

	


	//	PHP 5 Constructor		
	function __construct() {
		$ocww_basename = dirname ( plugin_basename ( __FILE__ ) );
		$widget_ops = array (
		'classname' => $ocww_basename, 
		'description' => __ ( 'ShopCode show product WooCommerce owl carousel on categories responsive ', $this->localizationDomain ) 
		);
		parent::__construct( $ocww_basename, __ ( 'Owl Carousel WooCommerce Widget', $this->localizationDomain ), $widget_ops );
	}
	
	
	function widget($args, $instance) {
		extract ( $args );
		$category = apply_filters ( 'category', isset ( $instance ['category'] ) ? esc_attr ( $instance ['category'] ) : '' );
		$count = apply_filters ( 'count', isset ( $instance ['count'] ) && is_numeric ( $instance ['count'] ) ? esc_attr ( $instance ['count'] ) : '' );		
		$idcount = apply_filters ( 'idcount', isset ( $instance ['idcount'] ) ? esc_attr ( $instance ['idcount'] ) : '' );
		$orderby = apply_filters ( 'orderby', isset ( $instance ['orderby'] ) ? $instance ['orderby'] : '' );
		$order = apply_filters ( 'order', isset ( $instance ['order'] ) ? $instance ['order'] : '' );
		$width = apply_filters ( 'width', isset ( $instance ['width'] ) && is_numeric ( $instance ['width'] ) ? $instance ['width'] : '180' );
		$height = apply_filters ( 'height', isset ( $instance ['height'] ) && is_numeric ( $instance ['height'] ) ? $instance ['height'] : '180' );
	    $show_post_thumb = apply_filters ( 'show_post_thumb', isset ( $instance ['show_post_thumb'] ) ? ( bool ) $instance ['show_post_thumb'] : ( bool ) false );
		echo $before_widget;
		
?>

<section class="owl-carousel-woocommerce-widget no-js"> 
<div class="owl-carousel-woocommerce-fluid">

<ul>
 <?php if($idcount ==0){ //Dont Slider
	$count = 5; 
 } ?>
 
 <div id="owl-slide-<?php echo $idcount; ?>" class="owl-carousel carousel-<?php echo $idcount; ?>">
<?php 
$wp_query = new WP_Query( array('post_type' => 'product', 'product_cat' => $category, 'posts_per_page' => $count, 'orderby' => $orderby, 'order' => $order, 'nopagging' => true));
$product_num=0; 
$i=1; while ($wp_query->have_posts()) : $wp_query->the_post(); global  $post, $product; $product_num ++ ?>

<?php 
$regular_price =  $product->regular_price;
$sale_price = $product->sale_price;
$id_product = $product->id;
?>



		<div class="item">
		<!-- GET IMAGE RESIZE -->
		<?php if ($show_post_thumb && has_post_thumbnail()) { ?>
		<?php $image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );  ?>
		<?php $params = array( 'width' => $width, 'height' => $height ); 
		$image = bfi_thumb( $image, $params );
		?>
		<!-- END GET IMAGE RESIZE -->

		 <a href="<?php the_permalink(); ?>">
		<img width="<?php echo $width; ?>" height="<?php echo $height; ?>"  class="lazy owl-lazy"  data-src="<?php echo $image; ?>"  alt="<?php the_title_attribute(); ?>" src="<?php echo $image; ?>" style="opacity: 1; display: block;">
		<h3><?php echo  get_the_title();  ?></h3>
		<div class="price">

		 <?php 
		if(($sale_price> 1)){ 
		 echo  number_format($sale_price).'$';
		 
		?>

		<?php } else {
		echo $product->get_price_html(); 
		}
		 ?>
		</div>
		</a>
			
			 <?php } ?>
		 
		 </div>

<?php
endwhile;
wp_reset_query();
wp_reset_postdata();
?>
</div> 

</ul>
<div class="clear"></div>
</div> 

</section> 

<?php
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
	
	function form($instance) {
		$category = isset ( $instance ['category'] ) ? esc_attr ( $instance ['category'] ) : '';
		$count = isset ( $instance ['count'] ) && is_numeric ( $instance ['count'] ) ? esc_attr ( $instance ['count'] ) : '10';		
		$idcount = isset ( $instance ['idcount'] )  ? esc_attr ( $instance ['idcount'] ) : '0';
		$orderby = isset ( $instance ['orderby'] ) ? $instance ['orderby'] : '';
		$order = isset ( $instance ['order'] ) ? $instance ['order'] : '';
		$width = isset ( $instance ['width'] ) && is_numeric ( $instance ['width'] ) ? $instance ['width'] : '180';
		$height = isset ( $instance ['height'] ) && is_numeric ( $instance ['height'] ) ? $instance ['height'] : '180';
		$show_post_thumb = isset ( $instance ['show_post_thumb'] ) ? ( bool ) $instance ['show_post_thumb'] : true;
		
		
?>

<p>
<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Product Woocomerce:', $this->localizationDomain); ?></label>
<select
id="<?php echo $this->get_field_id('category'); ?>"
	name="<?php echo $this->get_field_name('category'); ?>">
	<?php 
	$selected = __($category, $this->localizationDomain);
	
	echo '<option value="' . $selected . '" ' .( '' == $category ? 'selected="selected"' : '' ). '>'. __($category, $this->localizationDomain).'</option>';
	
	
	$cats = get_categories(array('hide_empty' => 1, 'taxonomy' => 'product_cat', 'hierarchical' => true));
	foreach ($cats as $cat) {
		echo '<option value="' . $cat->slug . '" ' .( $cat->term_id == $category ? 'selected="selected"' : '' ). '>' . $cat->name . '</option>';
	}
	
	?>
</select>
</p>
<p><label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby:', $this->localizationDomain); ?></label><select
	id="<?php echo $this->get_field_id('orderby'); ?>"
	name="<?php echo $this->get_field_name('orderby'); ?>">
	<option value="date"
		<?php echo 'date' == $orderby ? 'selected="selected"' : '' ?>><?php _e('Date', $this->localizationDomain); ?></option>
	<option value="ID"
		<?php echo 'ID' == $orderby ? 'selected="selected"' : '' ?>><?php _e('ID', $this->localizationDomain); ?></option>
	<option value="title"
		<?php echo 'title' == $orderby ? 'selected="selected"' : '' ?>><?php _e('Title', $this->localizationDomain); ?></option>
	<option value="author"
		<?php echo 'author' == $orderby ? 'selected="selected"' : '' ?>><?php _e('Author', $this->localizationDomain); ?></option>
	<option value="comment_count"
		<?php echo 'comment_count' == $orderby ? 'selected="selected"' : '' ?>><?php _e('Comment count', $this->localizationDomain); ?></option>
	<option value="rand"
		<?php echo 'rand' == $orderby ? 'selected="selected"' : '' ?>><?php _e('Random', $this->localizationDomain); ?></option>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:', $this->localizationDomain); ?></label>
<select
	id="<?php echo $this->get_field_id('order'); ?>"
	name="<?php echo $this->get_field_name('order'); ?>">
	<option value="DESC"
		<?php echo 'DESC' == $order ? 'selected="selected"' : '' ?>><?php _e('DESC:', $this->localizationDomain); ?></option>
	<option value="ASC"
		<?php echo 'ASC' == $order ? 'selected="selected"' : '' ?>><?php _e('ASC:', $this->localizationDomain); ?></option>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Product number:', $this->localizationDomain); ?> 
<input id="<?php echo $this->get_field_id('count'); ?>"
	name="<?php echo $this->get_field_name('count'); ?>" type="text"
	size="3" value="<?php echo $count; ?>" />
</label>
</p>
<p>
<label for="<?php echo $this->get_field_id('idcount'); ?>"><?php _e('Owl Style:', $this->localizationDomain); ?>
<select
	id="<?php echo $this->get_field_id('idcount'); ?>"
	name="<?php echo $this->get_field_name('idcount'); ?>">
	<option value="0"<?php echo '0' == $idcount ? 'selected="selected"' : '' ?>><?php _e('No Slider:', $this->localizationDomain); ?></option>
    <option value="owl-multiple"<?php echo 'owl-multiple' == $idcount ? 'selected="selected"' : '' ?>><?php _e('Owl Responsive 5 Product', $this->localizationDomain); ?></option>
	<option value="owl-multiple-4"<?php echo 'owl-multiple-4' == $idcount ? 'selected="selected"' : '' ?>><?php _e('Owl Responsive 4 Product', $this->localizationDomain); ?></option>
	<option value="owl-multiple-3"<?php echo 'owl-multiple-3' == $idcount ? 'selected="selected"' : '' ?>><?php _e('Owl Responsive 3 Product', $this->localizationDomain); ?></option>
	<option value="owl-multiple-2"<?php echo 'owl-multiple-2' == $idcount ? 'selected="selected"' : '' ?>><?php _e('Owl Responsive 2 Product', $this->localizationDomain); ?></option>
	<option value="owl-multiple-1"<?php echo 'owl-multiple-1' == $idcount ? 'selected="selected"' : '' ?>><?php _e('Owl Responsive 1 Product)', $this->localizationDomain); ?></option>
	
</select>
</label>
</p>	
<p>
<input id="<?php echo $this->get_field_id('show_post_thumb'); ?>"
	name="<?php echo $this->get_field_name('show_post_thumb'); ?>"
	type="checkbox" <?php checked($show_post_thumb); ?> /> <label
	for="<?php echo $this->get_field_id('show_post_thumb'); ?>"><?php _e('Show post thumb', $this->localizationDomain); ?></label><br />
<small><?php _e('Image Size (H-W):', $this->localizationDomain); ?></small>
<input type="text" size="3"
	name="<?php echo $this->get_field_name('width'); ?>"
	value="<?php echo $width; ?>" />px <input type="text" size="3"
	name="<?php echo $this->get_field_name('height'); ?>"
	value="<?php echo $height; ?>" />px
</p>

<?php 
    }
	
} // End Class Ocww_Owl_Carousel_Woocommerce_Widget

add_action('widgets_init', create_function('', 'return register_widget("Ocww_Owl_Carousel_Woocommerce_Widget");'));


 

?>