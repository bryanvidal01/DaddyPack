<?php

/* ----------------------------------------------------------
  Config
---------------------------------------------------------- */

/* Facebook
-------------------------- */

/* Obtain the page ID here : http://findmyfacebookid.com/ */

define( 'CLRZ_FACEBOOK_PAGEID', '305614209495539' );
define( 'CLRZ_FACEBOOK_CACHEDURATION', HOUR_IN_SECONDS );

/*
$elements = clrz_importerz_get_facebook();
$likes = clrz_importerz_get_facebook_count();
*/

/* Twitter
-------------------------- */

/* A specific application should be created : https://dev.twitter.com/apps/new */

define( 'CLRZ_TWITTER_USERNAME', 'Colorz' );
define( 'CLRZ_TWITTER_NB_TWEETS', '5' );
define( 'CLRZ_TWITTER_CACHEDURATION', 10 * MINUTE_IN_SECONDS );
define( 'CLRZ_TWITTER_TOKEN', '1530121524-wQ2d6EvWRU5iWemDZH5ckY1xLfV9oy3gqg45Yq8' );
define( 'CLRZ_TWITTER_TOKEN_SECRET', 'yA5c0pR97HnIVCzAFPldDMB03KJzxkCVQFEEslgs' );
define( 'CLRZ_TWITTER_CONSUMER_KEY', '3yahFFoY6dWf2dFdDtCw' );
define( 'CLRZ_TWITTER_CONSUMER_SECRET', 'ZaDrujJrqKDO1Nt63frBEhtAFy4wKqJxX48zr3Rl0M' );

/*
$tweets = clrz_importerz_get_tweets();
$counter = clrz_importerz_get_twitter_counter();
*/

/* ----------------------------------------------------------
  Facebook
---------------------------------------------------------- */

function clrz_importerz_get_facebook() {
    $rssfb_items = get_transient( 'feed_facebook_items_' .strtolower( CLRZ_FACEBOOK_PAGEID ) );
    if ( $rssfb_items === false ) {
        $rssfb_items = array();
        include_once ABSPATH . WPINC . '/feed.php';
        $rssfb = fetch_feed( 'https://www.facebook.com/feeds/page.php?format=atom10&id='.CLRZ_FACEBOOK_PAGEID );
        // Checks that the object is created correctly
        if ( ! is_wp_error( $rssfb ) ) {
            $maxitems = $rssfb->get_item_quantity( 5 );
            $items = $rssfb->get_items( 0, $maxitems );
            foreach ( $items as $item ) {
                $rssfb_items[] = array(
                    'type' => 'facebook',
                    'time' => $item->get_date( 'U' ),
                    'content' => esc_html( $item->get_title() ),
                    'link' => esc_html( $item->get_permalink() )
                );
            }
            set_transient( 'feed_facebook_items', $rssfb_items, CLRZ_FACEBOOK_CACHEDURATION );
        }
    }
    return $rssfb_items;
}

// Compteur Facebook
// http://w4dev.com/wp/facebook-page-like-count/
function clrz_importerz_get_facebook_count( ) {
    $transient = 'facebook_like_count_' . CLRZ_FACEBOOK_PAGEID;
    $url = 'http://graph.facebook.com/'. CLRZ_FACEBOOK_PAGEID;

    $value = get_transient( $transient );
    if ( !is_numeric( $value ) || '0' == $value ) {
        $content = wp_remote_retrieve_body( wp_remote_request( $url ) );

        if ( is_wp_error( $content ) )
            return $content->get_error_message();

        $content = json_decode( $content );
        $value = intval( $content->likes );
        set_transient( $transient, $value, CLRZ_FACEBOOK_CACHEDURATION );
    }
    return $value;
}

/* ----------------------------------------------------------
  Twitter
---------------------------------------------------------- */

function tweet_apply_entities( $content, $entities ) {
    if ( isset( $entities->hashtags ) ) {
        foreach ( $entities->hashtags as $hashtag ) {
            $content = str_replace( '#'.$hashtag->text, '<strong>#<a target="_blank" href="https://twitter.com/search?q=%23'.$hashtag->text.'">'.$hashtag->text.'</a></strong>', $content );
        }
    }
    if ( isset( $entities->urls ) ) {
        foreach ( $entities->urls as $url ) {
            $content = str_replace( $url->url, '<strong><a target="_blank" href="'.$url->expanded_url.'">'.$url->display_url.'</a></strong>', $content );
        }
    }

    if ( isset( $entities->user_mentions ) ) {
        foreach ( $entities->user_mentions as $user ) {
            $content = str_replace( '@'.$user->screen_name, '<strong>@<a target="_blank" href="https://twitter.com/'.$user->screen_name.'">'.$user->screen_name.'</a></strong>', $content );
        }
    }

    if ( isset( $entities->media ) ) {
        foreach ( $entities->media as $url ) {
            $content = str_replace( $url->url, '<strong><a target="_blank" href="'.$url->expanded_url.'">'.$url->display_url.'</a></strong>', $content );
        }
    }

    return $content;
}

function clrz_importerz_get_twitter_counter() {
    // Update twitts
    clrz_importerz_get_tweets();
    // display option
    return get_option( strtolower( CLRZ_TWITTER_USERNAME ).'_twitter_counter' );
}

function clrz_importerz_get_tweets() {
    $twitter_items = get_transient( 'twitter_feed_items_'.strtolower( CLRZ_TWITTER_USERNAME ).'' );

    if ( $twitter_items === false || !is_array( $twitter_items ) || empty( $twitter_items ) ) {

        $host = 'api.twitter.com';
        $method = 'GET';
        $path = '/1.1/statuses/user_timeline.json'; // api call path

        $query = array( // query parameters
            'screen_name' => CLRZ_TWITTER_USERNAME,
            'count' => CLRZ_TWITTER_NB_TWEETS,
            'exclude_replies' => 'true',
            'include_rts' => 'false'
        );

        $oauth = array(
            'oauth_consumer_key' => CLRZ_TWITTER_CONSUMER_KEY,
            'oauth_token' => CLRZ_TWITTER_TOKEN,
            'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
            'oauth_timestamp' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0'
        );

        $oauth = array_map( "rawurlencode", $oauth ); // must be encoded before sorting
        $query = array_map( "rawurlencode", $query );

        $arr = array_merge( $oauth, $query ); // combine the values THEN sort

        asort( $arr ); // secondary sort (value)
        ksort( $arr ); // primary sort (key)

        // http_build_query automatically encodes, but our parameters
        // are already encoded, and must be by this point, so we undo
        // the encoding step
        $querystring = urldecode( http_build_query( $arr, '', '&' ) );

        $url = "https://$host$path";

        // mash everything together for the text to hash
        $base_string = $method."&".rawurlencode( $url )."&".rawurlencode( $querystring );

        // same with the key
        $key = rawurlencode( CLRZ_TWITTER_CONSUMER_SECRET )."&".rawurlencode( CLRZ_TWITTER_TOKEN_SECRET );

        // generate the hash
        $signature = rawurlencode( base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) ) );

        // this time we're using a normal GET query, and we're only encoding the query params
        // (without the oauth params)
        $url .= "?".http_build_query( $query );
        $url=str_replace( "&amp;", "&", $url ); //Patch by @Frewuill

        $oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
        ksort( $oauth ); // probably not necessary, but twitter's demo does it

        // also not necessary, but twitter's demo does this too
        function add_quotes( $str ) { return '"'.$str.'"'; }
        $oauth = array_map( "add_quotes", $oauth );

        // this is the full value of the Authorization line
        $auth = "OAuth " . urldecode( http_build_query( $oauth, '', ', ' ) );

        // if you're doing post, you need to skip the GET building above
        // and instead supply query parameters to CURLOPT_POSTFIELDS
        $options = array( CURLOPT_HTTPHEADER => array( "Authorization: $auth" ),
            //CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false );

        $feed = curl_init();
        curl_setopt_array( $feed, $options );
        $twitter_json = curl_exec( $feed );
        curl_close( $feed );
        $twitter_counter = get_option( strtolower( CLRZ_TWITTER_USERNAME ).'_twitter_counter' );
        $twitter_items = array();
        $twitter_elements_json = json_decode( $twitter_json );
        foreach ( $twitter_elements_json as $element ) {
            $source = strtolower( $element->source );
            // We don't import element from facebook
            if ( strpos( $source, 'facebook' ) === false ) {
                $element_text = tweet_apply_entities( $element->text, $element->entities );
                if ( isset( $element->user->followers_count ) ) {
                    $twitter_counter = $element->user->followers_count;
                }
                $twitter_items[] = array(
                    'type' => 'twitter',
                    'time' => date( 'U', strtotime( $element->created_at ) ),
                    'content' => $element_text,
                    'link' => 'https://twitter.com/'.$query['screen_name'].'/status/'.$element->id_str
                );
            }
        }
        update_option( strtolower( CLRZ_TWITTER_USERNAME ).'_twitter_counter', $twitter_counter );
        set_transient( 'twitter_feed_items_'.strtolower( CLRZ_TWITTER_USERNAME ), $twitter_items, 10 * MINUTE_IN_SECONDS );
    }
    return $twitter_items;
}
