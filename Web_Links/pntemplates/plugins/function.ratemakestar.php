<?php
// $Id: function.ratemakestar.php,v 1.0 2005/05/23 20:12:22 petzi-juist Exp $
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

function smarty_function_ratemakestar($params, &$smarty)
{
    extract($params);
    unset($params);

    $score = number_format($linkratingsummary, pnModGetVar('Web_Links', 'mainvotedecimal'));

    $max_score = 10;
    $score /= 2;    $max_score /=2; //      5 stars. comment for 10 stars
    $basedir="modules/Web_Links/pnimages/" ;   // for $basedir/image/xxx.gif
    $rateImgFull = $basedir.'rate_full.gif';
    $rateImgHalf = $basedir.'rate_half.gif';
    $rateImgNone = $basedir.'rate_none.gif';

    // Break up score
    if (strpos($score,".")==0) {
        $full_stars=$score;
    }else {
        $full_stars=substr($score,0,strpos($score,"."));
    }

    // *** Is there half star
    if (substr($score,strpos($score,",")+1)==0) { //irgendwie buggy an dieser stelle?
        $half_stars=0;
    }else {
        $half_stars=1;
    }

    // *** Build Star Line
    $blank_stars=$max_score-($full_stars+$half_stars);
    $star_line="";
    for ($i=1;$i<=$max_score;$i++) {
        if ($i<=$full_stars) {
            $star_line.="<img src='".$rateImgFull."' alt=\"\" />";
        } elseif ($i<=($half_stars+$full_stars)) {
            $star_line.="<img src='".$rateImgHalf."' alt=\"\" />";
        } elseif ($i<=$max_score) {
            $star_line.="<img src='".$rateImgNone."' alt=\"\" />";
        }
    }
    return $star_line;
}
?>