<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SDFSO_Schema {
    
    private static $queue = array();
    private static $current = array();
    private static $faq_rendered = false;
    
    public static function start( $type, $args ) {
        // FAQPage: only one per page for Google Rich Results
        if ( 'FAQPage' === $type && self::$faq_rendered ) {
            return false;
        }
        
        $base = array(
            '@context' => 'https://schema.org',
            '@type'    => $type,
        );
        
        self::$current = array_merge( $base, $args );
        return true;
    }
    
    public static function add_step( $name, $text ) {
        self::$current['step'][] = array(
            '@type' => 'HowToStep',
            'name'  => sanitize_text_field( $name ),
            'text'  => wp_kses_post( $text ),
        );
    }
    
    public static function add_faq( $question, $answer ) {
        if ( ! isset( self::$current['mainEntity'] ) ) {
            self::$current['mainEntity'] = array();
        }
        self::$current['mainEntity'][] = array(
            '@type' => 'Question',
            'name'  => sanitize_text_field( $question ),
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => wp_kses_post( $answer ),
            ),
        );
    }
    
    public static function add_item( $name, $position = null, $url = null ) {
        if ( ! isset( self::$current['itemListElement'] ) ) {
            self::$current['itemListElement'] = array();
        }
        $item = array(
            '@type' => 'ListItem',
            'name'  => sanitize_text_field( $name ),
        );
        if ( $position ) {
            $item['position'] = (int) $position;
        }
        if ( $url ) {
            $item['url'] = esc_url_raw( $url );
        }
        self::$current['itemListElement'][] = $item;
    }
    
    public static function push() {
        if ( ! empty( self::$current['@type'] ) ) {
            // Validate required fields per type
            $valid = false;
            switch ( self::$current['@type'] ) {
                case 'HowTo':
                    $valid = ! empty( self::$current['name'] ) && ! empty( self::$current['step'] );
                    break;
                case 'FAQPage':
                    $valid = ! empty( self::$current['mainEntity'] );
                    if ( $valid ) {
                        self::$faq_rendered = true;
                    }
                    break;
                case 'ItemList':
                    $valid = ! empty( self::$current['name'] ) && ! empty( self::$current['itemListElement'] );
                    break;
                case 'CreativeWork':
                    $valid = ! empty( self::$current['name'] );
                    break;
            }
            
            if ( $valid ) {
                self::$queue[] = self::$current;
            }
        }
        self::$current = array();
    }
    
    public static function get_queue() {
        return self::$queue;
    }
    
}