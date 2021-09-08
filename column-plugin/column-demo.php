<?php

/**
 * Plugin Name: Our Column
 * Plugin Uri: https://column-demo
 * Description: This plugin will add some extra column to your WordPress posts column & You can see the thumbnail, post id etc
 * Author: SeJan ahmed BayZid
 * Version: 1.0
 * License: 
 * Text Domain: column-demo
 * Domain path: /languages
 */

//textdomain 
function col_demo_textdomain(){
    load_plugin_textdomain('column-demo', false, dirname(__FILE__) . '/languages');
}
add_action('plugin_loaded', 'col_demo_textdomain');



/*  
  adding and hiding the column from admin posts 
*/

// function col_demo_posts_column($column){
//     // print_r($column);

//     // this is how to hide  author /title /categories /tags /comments /date  etc
//     unset($column['author']);
//     unset($column['categories']);

//     // this is how to change the order of those column name from admin posts
//     $column['categories'] = "categories";
//     $column['author'] = "Author";

//     return $column;
// }
// add_filter('manage_posts_columns', 'col_demo_posts_column');



/**
 * To add a new column.
 */
function col_demo_adding_new_value($column)
{
    $column["id"] = __('Posts ID', 'column-demo');
    $column["thumbnail"] = __('Posts Thumbnail', 'column-demo');
    $column["wordcount"] = __('Word Count', 'column-demo');
    return $column;
}
add_filter('manage_posts_columns', 'col_demo_adding_new_value');

// add_action('manage_pages_custom_column', 'col_demo_posts_column', 10, 2); // to use with the page

function col_demo_posts_column( $column, $post_id ) {
	if ( 'id' == $column ) {
		echo $post_id;
	} elseif ( 'thumbnail' == $column ) {
		$thumbnail = get_the_post_thumbnail( $post_id, array( 100, 300 ) );
		echo $thumbnail;
	} elseif ( 'wordcount' == $column ) {
		$_post = get_post($post_id);
		$content = $_post->post_content;
		$wordn = str_word_count(strip_tags($content));
		// $wordn = get_post_meta( $post_id, 'wordn', true );
		echo $wordn;
	}
}
add_action('manage_posts_custom_column', 'col_demo_posts_column', 10, 2);



// adding filter to wordpress admin posts
function col_demo_top_filter()
{
    if (isset($_GET['post_type']) && $_GET['post_type'] != 'post') {   // display only on posts page
        return;
    }

    $filter_value = isset($_GET['DEMO-Filter']) ? $_GET['DEMO-Filter'] : '';
    $values = array(
        '0' => __('Select ALL', 'column-demo'),
        '1' => __('Select posts', 'column-demo'),
        '2' => __('Select posts++', 'column-demo'),
    )
?>
    <select name="DEMO-Filter">
        <?php
        foreach ($values as $key => $value) {
            printf(
                '<option value="%s" %s > %s </option>',
                $key,
                $key == $filter_value ? "selected" : '',
                $value,
            );
        }
        ?>
    </select>
<?php
}
add_action('restrict_manage_posts', 'col_demo_top_filter');


function col_demo_filter_data($wpquery)
{
    if (!is_admin()) {
        return;
    }

    $filter_value = isset($_GET['DEMO-Filter']) ? $_GET['DEMO-Filter'] : '';
    if ('1' == $filter_value) {
        $wpquery->set('post__in', array(172, 175, 167));  // display the post according to id
    } elseif ('2' == $filter_value) {
        $wpquery->set('post__in', array(100, 133, 170, 1));  // display the post according to id
    }
}
add_action('pre_get_posts', 'col_demo_filter_data');





//  Second Function
// Thumbnail column selector

function col_demo_thumbnail_filter() {
	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post' ) {    //display only on posts page
		return;
	}


	$filter_value = isset( $_GET['THFILTER'] ) ? $_GET['THFILTER'] : '';
	$values       = array(
		'0' => __( 'Thumbnail Status', 'column_demo' ),
		'1' => __( 'Has Thumbnail', 'column_demo' ),
		'2' => __( 'No Thumbnail', 'column_demo' ),
	);
	?>
    <select name="THFILTER">
		<?php
		foreach ( $values as $key => $value ) {
			printf( "<option value='%s' %s>%s</option>", $key,
				$key == $filter_value ? "selected = 'selected'" : '',
				$value
			);
		}
		?>
    </select>
	<?php
}

add_action( 'restrict_manage_posts', 'col_demo_thumbnail_filter' );



function col_demo_thumbnail_filter_data( $wpquery ) {
	if ( ! is_admin() ) {
		return;
	}


	$filter_value = isset( $_GET['THFILTER'] ) ? $_GET['THFILTER'] : '';

	if ( '1' == $filter_value ) {
		$wpquery->set( 'meta_query', array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS'
			)
		) );
	} else if ( '2' == $filter_value ) {
		$wpquery->set( 'meta_query', array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS'
			)
		) );
	}
}

add_action( 'pre_get_posts', 'col_demo_thumbnail_filter_data' );






/**
 *  display the post according to the total words 
 */
/* function col_demo_wc_filter()
{
    if (isset($_GET['post_type']) && $_GET['post_type'] != 'post') {   // display only on posts page
        return;
    }

    $filter_value = isset($_GET['WCFILTER']) ? $_GET['WCFILTER'] : '';
    $values = array(
        '0' => __('Posts Word Count', 'column-demo'),
        '1' => __('Above 400', 'column-demo'),
        '2' => __('200 to 400', 'column-demo'),
        '3' => __('Below 200', 'column-demo'),
    )
?>
    <select name="WCFILTER">
        <?php
        foreach ($values as $key => $value) {
            printf(
                '<option value="%s" %s > %s </option>',
                $key,
                $key == $filter_value ? "selected" : '',
                $value,
            );
        }
        ?>
    </select>
<?php
}
add_action('restrict_manage_posts', 'col_demo_wc_filter'); */


// word-count data
/* function col_demo_wc_data($wpquery)
{
    if (!is_admin()) {
        return;
    }

    $filter_value = isset( $_GET['WCFILTER']) ? $_GET['WCFILTER'] : '' ;
    if ( '1' == $filter_value) {
        $wpquery->set('meta_query', array(
            array(
                'key' => 'wordn',
                'value' => 400,
                'compare' => '>=',
                'type' => 'NUMERIC'
            )
        ));
    } elseif ('2' == $filter_value) {
        $wpquery->set('meta_query', array(
            array(
                'key' => 'wordn',
                'value' => array(200, 400),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            )
        ));
    }elseif ('3' == $filter_value) {
        $wpquery->set('meta_query', array(
            array(
                'key' => 'wordn',
                'value' => 200,
                'compare' => '<=',
                'type' => 'NUMERIC'
            )
        ));
    }
}
add_action('pre_get_posts', 'col_demo_wc_data'); */