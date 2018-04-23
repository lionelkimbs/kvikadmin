<?php

namespace Kvik\AdminBundle\Services;

class Sanitize{

    public function slugify($str, $name, $charset = 'utf-8'){
        if( $str == null ) $str = $name;
        $str = htmlentities( $str, ENT_NOQUOTES, $charset );
        $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
        $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
        $str = preg_replace( '#&[^;]+;#', '-', $str );
        $str = preg_replace( '/ +/', '-', $str );
        return mb_strtolower($str);
    }
}
