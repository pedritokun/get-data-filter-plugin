<?php 

$args = array(
        'posts_per_page' => 12,
        'post_type' => 'article',
        'post_status' => 'publish',
        'order' => 'DESC',
        'orderby' => 'date',
        'tax_query' => array(
            'relation' => 'AND',
            array(
                    'taxonomy' => 'sector',
                    'field' => 'id',
                    'terms' => array(22, 30, 109),
                    'include_children' => false,
                    'operator' => 'NOT IN'
            )
        ),
        'meta_key' => 'is_print',
        'meta_value' => '0',
        'meta_query' => array(
            'relation' => 'OR' ,
            array(
                    'key' => 'article_type',
                    'value' => '1',
                    'compare' => 'LIKE'
                ),
            array(
                    'key' => 'article_type',
                    'value' => '4',
                    'compare' => 'LIKE'
                )
            )
    );
$cont = 0;
// The Query
$the_query = new WP_Query( $args );
 
// The Loop
if ( $the_query->have_posts() ) {
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $id = get_the_ID();
        $title = get_the_title();

        $term_list = wp_get_post_terms(get_the_ID(), 'sector');
        foreach ( $term_list as $term ) {
            if( $term->parent == 0 ){
                $sector = $term->name;
            }
        }

        $sectorupper = mb_strtoupper($sector);

        $nicedate = get_the_date('g:i A | j F Y');
        $date = get_the_date('Y-m-d g:i:s');
        $summary = get_post_meta(get_the_ID(), 'summary', true);

        $url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );

        $legal = 'images/default-LEGAL.jpg';
        $economia = 'images/default-ECONOMIA.jpg';
        $finanzas = 'images/default-FINANZAS.jpg';
        $sectores = 'images/default-SECTORES.jpg';

        if($url == ''){
            switch ($sector) {
                case 'Legal y Política':
                    $sql = "INSERT INTO $table_name (postid,title,sector,nicedate,date,content,image) 
                    VALUES ($id,'$title','$sectorupper','$nicedate','$date','$summary','$legal')";
                    break;
                case 'Economía':
                    $sql = "INSERT INTO $table_name (postid,title,sector,nicedate,date,content,image) 
                    VALUES ($id,'$title','$sectorupper','$nicedate','$date','$summary','$economia')";
                    break;
                case 'Mercados y Finanzas':
                    $sql = "INSERT INTO $table_name (postid,title,sector,nicedate,date,content,image) 
                    VALUES ($id,'$title','$sectorupper','$nicedate','$date','$summary','$finanzas')";
                    break;
                default:
                // Sectores y Empresas
                    $sql = "INSERT INTO $table_name (postid,title,sector,nicedate,date,content,image) 
                    VALUES ($id,'$title','$sectorupper','$nicedate','$date','$summary','$sectores')";
                    break;
            }
            
        }else{
            $sql = "INSERT INTO $table_name (postid,title,sector,nicedate,date,content,image) 
            VALUES ($id,'$title','$sectorupper','$nicedate','$date','$summary','$url')";
        }

        $dbh->query( $sql );       
    }

} else {
    // no posts found
}

/* Restore original Post Data */
wp_reset_postdata();

?>