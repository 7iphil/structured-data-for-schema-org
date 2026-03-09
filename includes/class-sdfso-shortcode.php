<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SDFSO_Shortcode {
    
    public function register() {
        add_shortcode( 'schema', array( $this, 'render' ) );
        add_filter( 'the_content', array( $this, 'fix_multiline' ), 0 );
    }
    
    public function fix_multiline( $content ) {
        return preg_replace_callback(
            '/\[schema([^\]]*?)\](.*?)\[\/schema\]/s',
            array( $this, 'normalize_shortcode' ),
            $content
        );
    }
    
    private function normalize_shortcode( $matches ) {
        $attrs = preg_replace( '/[\r\n\t]+/', ' ', $matches[1] );
        $attrs = preg_replace( '/\s+/', ' ', $attrs );
        $inner = trim( $matches[2] );
        $inner = preg_replace( '/^<\/?p[^>]*?>/i', '', $inner );
        $inner = preg_replace( '/<\/?p[^>]*?>$/i', '', $inner );
        return '[schema' . $attrs . ']' . $inner . '[/schema]';
    }
    
    public function render( $atts, $content = '' ) {
        // Prevent duplicate processing
        static $processed = array();
        $key = md5( wp_json_encode( $atts ) . $content );
        if ( isset( $processed[ $key ] ) ) {
            return $this->get_html_output( $atts, $content );
        }
        $processed[ $key ] = true;
        
        $defaults = array(
            'type'        => 'HowTo',
            'name'        => '',
            'description' => '',
            'time'        => '',
            'image'       => '',
            'hidden'      => false,
            'position'    => '',
            'url'         => '',
            'items-tag'   => '',
        );
        
        $atts = shortcode_atts( $defaults, $atts, 'schema' );
        
        $type = sanitize_text_field( $atts['type'] );
        $allowed_types = array( 'HowTo', 'FAQPage', 'ItemList', 'CreativeWork' );
        if ( ! in_array( $type, $allowed_types, true ) ) {
            $type = 'HowTo';
        }
        
        // Start schema
        $args = array();
        if ( ! empty( $atts['name'] ) ) {
            $args['name'] = sanitize_text_field( $atts['name'] );
        }
        if ( ! empty( $atts['description'] ) ) {
            $args['description'] = sanitize_textarea_field( $atts['description'] );
        }
        if ( ! empty( $atts['time'] ) && 'HowTo' === $type ) {
            $args['totalTime'] = $this->parse_time( $atts['time'] );
        }
        if ( ! empty( $atts['image'] ) && 'HowTo' === $type ) {
            $args['image'] = esc_url_raw( trim( $atts['image'] ) );
        }
        
        $started = SDFSO_Schema::start( $type, $args );
        
        if ( ! $started ) {
            // FAQPage already rendered
            return '';
        }
        
        // Process content based on type
        switch ( $type ) {
            case 'HowTo':
                if ( ! empty( $content ) ) {
                    $this->parse_howto_steps( $content );
                }
                break;

            case 'FAQPage':
                if ( ! empty( $content ) ) {
                    $this->parse_faq_items( $content, $atts );
                }
                break;
            
            case 'ItemList':
                if ( ! empty( $content ) ) {
                    $this->parse_list_items( $content, $atts );
                }
                break;
            
            case 'CreativeWork':
                // No special parsing needed
                break;
        }
        
        SDFSO_Schema::push();
        
        return $this->get_html_output( $atts, $content );
    }
    
    private function parse_list_items( $content, $atts ) {
        $position = ! empty( $atts['position'] ) ? (int) $atts['position'] : null;
        $lines = array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( $content ) ) ) );
        foreach ( $lines as $line ) {
            if ( ! empty( $line ) ) {
                $pos = $position ? $position++ : null;
                SDFSO_Schema::add_item( $line, $pos, ! empty( $atts['url'] ) ? $atts['url'] : null );
            }
        }
    }

    private function parse_faq_items( $content, $atts ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( $content ) ) ) );
        foreach ( $lines as $line ) {
            $parts = explode( '|', $line, 2 );
            if ( 2 === count( $parts ) ) {
                SDFSO_Schema::add_faq( trim( $parts[0] ), trim( $parts[1] ) );
            }
        }
    }

    private function parse_howto_steps( $content ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( $content ) ) ) );
        $index = 0;
        foreach ( $lines as $line ) {
            if ( ! empty( $line ) && '{' !== substr( trim( $line ), 0, 1 ) ) {
                $index++;
                SDFSO_Schema::add_step( 'Step ' . $index, $line );
            }
        }
    }

    private function get_html_output( $atts, $content ) {
        $hide = $atts['hidden'] ? ' style="display:none"' : '';
        $type = strtolower( esc_html( $atts['type'] ) );
        $name = ! empty( $atts['name'] ) ? esc_html( $atts['name'] ) : 'Schema';
        
        $html = '<div class="schema-block schema-' . $type . '"' . $hide . '>';
        
        // Show title for all types
        if ( ! empty( $atts['name'] ) ) {
            $html .= '<h3>' . $name . '</h3>';
        }
        
        // Type-specific HTML output
        switch ( $atts['type'] ) {
            case 'FAQPage':
                $html .= $this->get_faq_html( $content );
                break;
            
            case 'ItemList':
                $html .= $this->get_list_html( $content, $atts );
                break;
            
            default:
                // HowTo, CreativeWork - default output
                if ( ! empty( $atts['description'] ) ) {
                    $html .= '<p>' . esc_html( $atts['description'] ) . '</p>';
                }
                if ( ! empty( $atts['time'] ) ) {
                    $html .= '<p><strong>Time:</strong> ' . esc_html( $atts['time'] ) . '</p>';
                }
                if ( ! empty( $content ) ) {
                    $html .= '<div>' . wp_kses_post( $content ) . '</div>';
                }
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render FAQPage as proper Q&A blocks
     */
    private function get_faq_html( $content ) {
        $html = '<div class="faq-list">';
        
        $lines = array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( $content ) ) ) );
        
        foreach ( $lines as $line ) {
            $parts = explode( '|', $line, 2 );
            if ( 2 === count( $parts ) ) {
                $question = trim( $parts[0] );
                $answer = trim( $parts[1] );
                
                // Use semantic HTML: dl/dt/dd or div structure
                $html .= '<div class="faq-item">';
                $html .= '<div class="faq-question"><strong>Q:</strong> ' . esc_html( $question ) . '</div>';
                $html .= '<div class="faq-answer"><strong>A:</strong> ' . esc_html( $answer ) . '</div>';
                $html .= '</div>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render ItemList as ordered/unordered list
     */
    private function get_list_html( $content, $atts ) {

        $tag = $atts['items-tag'] === 'ul' ? $atts['items-tag'] : 'ol';
        
        $html = '<' . $tag . ' class="item-list">';
        
        $lines = array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( $content ) ) ) );
        
        foreach ( $lines as $line ) {
            if ( ! empty( $line ) ) {
                $html .= '<li class="item-list-item">' . esc_html( $line ) . '</li>';
            }
        }
        
        $html .= '</' . $tag . '>';
        
        return $html;
    }
    
    private function parse_time( $duration ) {
        $duration = strtoupper( trim( $duration ) );
        if ( strpos( $duration, 'PT' ) === 0 ) {
            return $duration;
        }
        $hours = 0;
        $minutes = 0;
        preg_match_all( '/(\d+)(H|M)/', $duration, $matches, PREG_SET_ORDER );
        foreach ( $matches as $match ) {
            if ( 'H' === $match[2] ) {
                $hours = (int) $match[1];
            } elseif ( 'M' === $match[2] ) {
                $minutes = (int) $match[1];
            }
        }
        if ( is_numeric( $duration ) ) {
            $minutes = (int) $duration;
        }
        return sprintf( 'PT%dH%dM', $hours, $minutes );
    }
}