<?php

$cachebust = '?v=19';
//$cachebust = '?v=' . rand(5, 15);

/* Welcome to Bones :)
This is the core Bones file where most of the
main functions & features reside. If you have
any custom functions, it's best to put them
in the functions.php file.

Developed by: Eddie Machado
URL: http://themble.com/bones/

  - head cleanup (remove rsd, uri links, junk css, ect)
  - enqueueing scripts & styles
  - theme support functions
  - custom menu output & fallbacks
  - related post function
  - page-navi function
  - removing <p> from around images
  - customizing the post excerpt

*/

/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function bones_head_cleanup() {
	// category feeds
	// remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	// remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	// remove WP version from css
	add_filter( 'style_loader_src', 'bones_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'bones_remove_wp_ver_css_js', 9999 );

} /* end bones head cleanup */

// A better title
// http://www.deluxeblogtips.com/2012/03/better-title-meta-tag.html
function rw_title( $title, $sep, $seplocation ) {
  global $page, $paged;

  // Don't affect in feeds.
  if ( is_feed() ) return $title;

  // Add the blog's name
  if ( 'right' == $seplocation ) {
    $title .= get_bloginfo( 'name' );
  } else {
    $title = get_bloginfo( 'name' ) . $title;
  }

  // Add the blog description for the home/front page.
  $site_description = get_bloginfo( 'description', 'display' );

  if ( $site_description && ( is_home() || is_front_page() ) ) {
    $title .= " {$sep} {$site_description}";
  }

  // Add a page number if necessary:
  if ( $paged >= 2 || $page >= 2 ) {
    $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
  }

  return $title;

} // end better title

// remove WP version from RSS
function bones_rss_version() { return ''; }

// remove WP version from scripts
function bones_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function bones_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function bones_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function bones_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function bones_scripts_and_styles() {

  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
  global $cachebust; 
  
  if (!is_admin()) {

		// modernizr (without media query polyfill)
		wp_register_script( 'bones-modernizr', get_stylesheet_directory_uri() . '/library/js/libs/modernizr.custom.min.js', array(), '2.5.3', false );

		// register main stylesheet
		wp_register_style( 'bones-stylesheet', get_stylesheet_directory_uri() . '/library/css/style.css' . $cachebust , array(), '', 'all' );

      // register main stylesheet
      wp_register_style( 'royalslider-stylesheet', get_stylesheet_directory_uri() . '/library/css/royalslider.css', array(), '', 'all' );
      wp_register_style( 'royalslider-skin', get_stylesheet_directory_uri() . '/library/css/rs-default.css', array(), '', 'all' );

      wp_register_style( 'swiper-stylesheet', get_stylesheet_directory_uri() . '/library/css/swiper.min.css', array(), '', 'all' );

      //font
      wp_register_style( 'googlefont-stylesheet', 'https://fonts.googleapis.com/css?family=Lato|Source+Sans+Pro:300italic,300', array(), '', 'all' );

          // ie-only style sheet
		wp_register_style( 'bones-ie-only', get_stylesheet_directory_uri() . '/library/css/ie.css', array(), '' );

    // comment reply script for threaded comments
    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
		  wp_enqueue_script( 'comment-reply' );
    }

		//adding scripts file in the footer
		wp_register_script( 'bones-js', get_stylesheet_directory_uri() . '/library/js/scripts.js', array( 'jquery' ), '', true );
        wp_register_script( 'fitvids-js', get_stylesheet_directory_uri() . '/library/js/libs/jquery.fitvids.js', array(), '', true );
        wp_register_script( 'swiper-js', get_stylesheet_directory_uri() . '/library/js/libs/swiper.jquery.min.js', array(), '', true );
        wp_register_script( 'slidebars-js', get_stylesheet_directory_uri() . '/library/js/libs/slidebars.min.js', array(), '', true );
        wp_register_script( 'hammer-js', get_stylesheet_directory_uri() . '/library/js/libs/hammer.min.js', array(), '', true );
        wp_register_script( 'sticky-js', get_stylesheet_directory_uri() . '/library/js/libs/sticky.min.js', array(), '', true );
        wp_register_script( 'mailchimp-forms-js', get_stylesheet_directory_uri() . '/library/js/utils/MailchimpForms.js', array(), '', true );
        wp_register_script( 'logger-js', get_stylesheet_directory_uri() . '/library/js/libs/logger.min.js', array(), '', true );
        wp_register_script( 'paper-js', get_stylesheet_directory_uri() . '/library/js/libs/paper/paper-core.min.js'. $cachebust, array(), '', true );
        wp_register_script( 'filesaver-js', get_stylesheet_directory_uri() . '/library/js/libs/FileSaver.min.js', array(), '', true );
        wp_register_script( 'isotope-js', get_stylesheet_directory_uri() . '/library/js/libs/isotope.pkgd.min.js', array(), '', true );
        wp_register_script( 'plyr-js', get_stylesheet_directory_uri() . '/library/js/libs/plyr.js', array(), '', true );
        wp_register_script( 'tweenmax-js', get_stylesheet_directory_uri() . '/library/js/libs/greensock/TweenMax.min.js', array(), '', true );
        wp_register_script( 'timelinemax-js', get_stylesheet_directory_uri() . '/library/js/libs/greensock/TimelineMax.min.js', array(), '', true );
        wp_register_script( 'scrollmagic-js', get_stylesheet_directory_uri() . '/library/js/libs/scrollmagic/ScrollMagic.min.js', array(), '', true );
        wp_register_script( 'scrollmagic-gsap-js', get_stylesheet_directory_uri() . '/library/js/libs/scrollmagic/animation.gsap.min.js', array(), '', true );
        wp_register_script( 'tweenmax-scroll-js', get_stylesheet_directory_uri() . '/library/js/libs/greensock/plugins/ScrollToPlugin.min.js', array(), '', true );
        wp_register_script( 'scrollmagic-debug-js', get_stylesheet_directory_uri() . '/library/js/libs/scrollmagic/uncompressed/plugins/debug.addIndicators.js', array(), '', true );
        wp_register_script( 'iframe-resizer-js', get_stylesheet_directory_uri() . '/library/js/libs/iframeResizer.min.js', array(), '', true );
        wp_register_script( 'imagesloaded-js', get_stylesheet_directory_uri() . '/library/js/libs/imagesloaded.pkgd.min.js', array(), '', true );
        wp_register_script( 'lazy-js', get_stylesheet_directory_uri() . '/library/js/libs/lazyload.min.js', array(), '', true );
        wp_register_script( 'cookie-manager-js', get_stylesheet_directory_uri() . '/library/js/utils/CookieManager.js' . $cachebust , array(), '', true );
        wp_register_script( 'charts-js', get_stylesheet_directory_uri() . '/library/js/libs/Chart.min.js' . $cachebust , array(), '', true );
        wp_register_script( 'charts-deferred-js', get_stylesheet_directory_uri() . '/library/js/libs/chartjs-plugin-deferred.min.js' . $cachebust , array(), '', true );
        wp_register_script( 'charts-manager-js', get_stylesheet_directory_uri() . '/library/js/app/ChartsManager.js' . $cachebust , array(), '', true );
        wp_register_script( 'paperview-js', get_stylesheet_directory_uri() . '/library/js/app/PaperView.js' . $cachebust , array(), '', true );
        wp_register_script( 'couplesview-js', get_stylesheet_directory_uri() . '/library/js/app/CouplesView.js' . $cachebust , array(), '', true );
        wp_register_script( 'twitter-controller-js', get_stylesheet_directory_uri() . '/library/js/utils/TwitterShareController.js' . $cachebust , array(), '', true );
        wp_register_script( 'newspage-js', get_stylesheet_directory_uri() . '/library/js/app/pages/NewsPage.js' . $cachebust , array(), '', true );
        wp_register_script( 'projectspage-js', get_stylesheet_directory_uri() . '/library/js/app/pages/ProjectsPage.js' . $cachebust , array(), '', true );
        wp_register_script( 'animation-controller-js', get_stylesheet_directory_uri() . '/library/js/app/AnimationController.js' . $cachebust , array(), '', true );
        wp_register_script( 'video-poster-js', get_stylesheet_directory_uri() . '/library/js/utils/VideoPoster.js', array(), '', true );
        wp_register_script( 'projects-filter-js', get_stylesheet_directory_uri() . '/library/js/app/ProjectsFilter.js', array(), '', true );

        wp_register_script( 'mapcontroller-js', get_stylesheet_directory_uri() . '/library/js/app/MapController.js', array(), '', true );
        wp_register_script( 'controller-js', get_stylesheet_directory_uri() . '/library/js/app/Controller.js' . $cachebust , array(), '', true );

        //adding min version of js
        wp_register_script( 'app-min-js', get_stylesheet_directory_uri() . '/library/js/app.min.js' . $cachebust, array(), '', true );

		// enqueue styles and scripts
		wp_enqueue_script( 'bones-modernizr' );
		wp_enqueue_style( 'googlefont-stylesheet' );
		wp_enqueue_style( 'bones-stylesheet' );
		wp_enqueue_style( 'bones-ie-only' );

		$wp_styles->add_data( 'bones-ie-only', 'conditional', 'lt IE 9' ); // add conditional wrapper around ie stylesheet

		/*
		I recommend using a plugin to call jQuery
		using the google cdn. That way it stays cached
		and your site will load faster.
        */

		wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'paper-js' );
        wp_enqueue_script( 'plyr-js' );
        wp_enqueue_script( 'charts-js' );
        wp_enqueue_script( 'charts-manager-js' );

        /*
		wp_enqueue_script( 'bones-js' );
		wp_enqueue_script( 'fitvids-js' );
		wp_enqueue_script( 'logger-js' );
		wp_enqueue_script( 'scrollmagic-js' );
		wp_enqueue_script( 'scrollmagic-gsap-js' );
		wp_enqueue_script( 'scrollmagic-debug-js' );
		wp_enqueue_script( 'tweenmax-scroll-js' );
		wp_enqueue_script( 'tweenmax-js' );
		wp_enqueue_script( 'timelinemax-js' );
		wp_enqueue_script( 'timelinemax-js' );
		wp_enqueue_script( 'lazy-js' );
		wp_enqueue_script( 'hammer-js' );
		wp_enqueue_script( 'sticky-js' );
		wp_enqueue_script( 'mailchimp-forms-js' );
		wp_enqueue_script( 'slider-js' );
		wp_enqueue_script( 'isotope-js' );
		wp_enqueue_script( 'twitter-controller-js' );
		wp_enqueue_script( 'animation-controller-js' );
		wp_enqueue_script( 'cookie-manager-js' );
		wp_enqueue_script( 'imagesloaded-js' );
		wp_enqueue_script( 'filesaver-js' );
		wp_enqueue_script( 'paperview-js' );
		wp_enqueue_script( 'couplesview-js' );
		wp_enqueue_script( 'newspage-js' );
		wp_enqueue_script( 'projectspage-js' );
		wp_enqueue_script( 'video-poster-js' );
		wp_enqueue_script( 'projects-filter-js' );
        wp_enqueue_script( 'mapcontroller-js' );
		wp_enqueue_script( 'controller-js' );
        */

       wp_enqueue_script( 'app-min-js' );

	}
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function bones_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// wp custom background (thx to @bransonwerner for update)
	add_theme_support( 'custom-background',
	    array(
	    'default-image' => '',    // background image default
	    'default-color' => '',    // background color default (dont add the #)
	    'wp-head-callback' => '_custom_background_cb',
	    'admin-head-callback' => '',
	    'admin-preview-callback' => ''
	    )
	);

	// rss thingy
	add_theme_support('automatic-feed-links');

	// to add header image support go here: http://themble.com/support/adding-header-background-image-support/

	// adding post format support
	add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	);

	// wp menus
	add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'bonestheme' ),   // main nav in header
		)
	);

	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array(
		'comment-list',
		'search-form',
		'comment-form'
	) );

} /* end bones theme support */


/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using bones_related_posts(); )
function bones_related_posts() {
	echo '<ul id="bones-related-posts">';
	global $post;
	$tags = wp_get_post_tags( $post->ID );
	if($tags) {
		foreach( $tags as $tag ) {
			$tag_arr .= $tag->slug . ',';
		}
		$args = array(
			'tag' => $tag_arr,
			'numberposts' => 5, /* you can change this to show more */
			'post__not_in' => array($post->ID)
		);
		$related_posts = get_posts( $args );
		if($related_posts) {
			foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
				<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; }
		else { ?>
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'bonestheme' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_postdata();
	echo '</ul>';
} /* end bones related posts function */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function bones_page_navi() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  echo '<nav class="pagination">';
  echo paginate_links( array(
    'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    'format'       => '',
    'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '&larr;',
    'next_text'    => '&rarr;',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3
  ) );
  echo '</nav>';
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function bones_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function bones_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="excerpt-read-more" href="'. get_permalink( $post->ID ) . '" title="'. __( 'Read ', 'bonestheme' ) . esc_attr( get_the_title( $post->ID ) ).'">'. __( 'Read more &raquo;', 'bonestheme' ) .'</a>';
}



?>
