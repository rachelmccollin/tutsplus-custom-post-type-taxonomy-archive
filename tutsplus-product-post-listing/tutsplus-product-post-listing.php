<?php
/**
 * Plugin Name:	Tuts+ Add Taxonomy Archive to Custom Post Types
 * Plugin URI:	https://github.com/rachelmccollin/tutsplus-custom-post-type-taxonomy-archive
 * Description:	Uses a custom taxonomy to add relevant blog posts to custom post type pages
 * Version:		1.0
 * Author: 		Rachel McCollin
 * Textdomain:	tutsplus
 * Author URI:	http://rachelmccollin.com
 *
 */
 
/*****************************************************************************
Register an extra post type for content with taxonomy
*****************************************************************************/
function tutsplus_register_product_post_type() {
	
	// book blurbs
	$labels = array( 
		'name'					=> 	__( 'Products' ),
		'singular_name' 		=>	__( 'Product' ),
		'add_new' 				=>	__( 'New Product' ),
		'add_new_item' 			=> 	__( 'Add New Product' ),
		'edit_item' 			=> 	__( 'Edit Product' ),
		'new_item' 				=> 	__( 'New Product' ),
		'view_item' 			=> 	__( 'View Product' ),
		'search_items' 			=> 	__( 'Search Products' ),
		'not_found' 			=>  __( 'No Products Found' ),
		'not_found_in_trash' 	=> 	__( 'No Products found in Trash' ),
	);
	$args = array(
		'labels' => $labels,
		'has_archive' => true,
		'public' => true,
		'hierarchical' => false,
		'supports' => array(
			'title', 
			'editor', 
			'excerpt', 
			'custom-fields', 
			'thumbnail',
			'page-attributes'
		),
		'rewrite'   => array( 'slug' => 'product' ),

	);
	register_post_type( 'tutsplus_product', $args );
	
}
add_action( 'init', 'tutsplus_register_product_post_type' );
	
 /********************************************************************************************
tutsplus_register_product_taxonomy - registers the taxonomy
 ********************************************************************************************/
function tutsplus_register_product_taxonomy() {
	
	// product taxonomy
	$labels = array(
		'name'              => __( 'Products', 'tutsplus' ),
		'singular_name'     => __( 'Product', 'tutsplus' ),
		'search_items'      => __( 'Search Products', 'tutsplus' ),
		'all_items'         => __( 'All Products', 'tutsplus' ),
		'edit_item'         => __( 'Edit Product', 'tutsplus' ),
		'update_item'       => __( 'Update Product', 'tutsplus' ),
		'add_new_item'      => __( 'Add New Product', 'tutsplus' ),
		'new_item_name'     => __( 'New Product Name', 'tutsplus' ),
		'menu_name'         => __( 'Product Taxonomy Term', 'tutsplus' ),
	);
	
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'sort' => true,
		'args' => array( 'orderby' => 'term_order' ),
		'rewrite' => array( 'slug' => 'book' ),
		'show_admin_column' => true
	);
	
	register_taxonomy( 'tutsplus_product_tax', array( 'tutsplus_product', 'post', 'page' ), $args);
		
}
add_action( 'init', 'tutsplus_register_product_taxonomy' );


 /********************************************************************************************
tutsplus_add_posts_to_product_pages - adds the taxonomy archive to product pages
 ********************************************************************************************/
function tutsplus_add_posts_to_product_pages() {
	
	// check if we're in the product post type
	if( is_singular( 'tutsplus_product' ) ) {		
		
		// fetch taxonomy terms for current product
		$productterms = get_the_terms( get_the_ID(), 'tutsplus_product_tax'  );
		
		if( $productterms ) {
			
			$producttermnames[] = 0;
					
			foreach( $productterms as $productterm ) {	
				
				$producttermnames[] = $productterm->name;
			
			}
			
						
			// set up the query arguments
			$args = array (
				'post_type' => 'post',
				'tax_query' => array(
					array(
						'taxonomy' => 'tutsplus_product_tax',
						'field'    => 'slug',
						'terms'    => $producttermnames,
					),
				),
			);
			
			// run the query
			$query = new WP_Query( $args );	

			if( $query->have_posts() ) { ?>
				
				<section class="product-related-posts">
			
				<?php echo '<h2>' . __( 'Related Posts', 'tutsplus' ) . '</h2>'; ?>
			
					<ul class="product-posts">
			
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					
						<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							
						<?php endwhile; ?>
						
						<?php wp_reset_postdata(); ?>
					
					</ul>
					
				</section>
				
			<?php }
			
		
		}
		
	}

}
add_action( 'suki/frontend/after_main', 'tutsplus_add_posts_to_product_pages' );
