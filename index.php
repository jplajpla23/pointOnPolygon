<?php


/* is (lon, lat) inside the polygon $p?
 * use ray casting algorithm (http://en.wikipedia.org/wiki/Point_in_polygon)
 * ie. project a horizontal line from our point to each segment
 * code adapted from http://stackoverflow.com/questions/14149099/raycasting-algorithm-with-gps-coordinates
 */
function inside_polygon($test_point, $points) {


    $p0 = end($points);
    $ctr = 0;
    foreach ( $points as $p1 ) {

        // there is a bug with this algorithm, when a point in "on" a vertex
        // in that case just add an epsilon
        if ($test_point[1] == $p0[1])
            $test_point[1]+=0.0000000001; #epsilon
    
        // ignore edges of constant latitude (yes, this is correct!)
        if ( $p0[1] != $p1[1] ) {
            // scale latitude of $test_point so that $p0 maps to 0 and $p1 to 1:
            $interp = ($test_point[1] - $p0[1]) / ($p1[1] - $p0[1]);
    
            // does the edge intersect the latitude of $test_point?
            // (note: use >= and < to avoid double-counting exact endpoint hits)
            if ( $interp >= 0 && $interp < 1 ) {
                // longitude of the edge at the latitude of the test point:
                // (could use fancy spherical interpolation here, but for small
                // regions linear interpolation should be fine)
                $long = $interp * $p1[0] + (1 - $interp) * $p0[0];
                // is the intersection east of the test point?
                if ( $long > $test_point[0] ) {
                    // if so, count it:
                    $ctr++;
    #echo "YES &$test_point[0],$test_point[1] ($p0[0],$p0[1])x($p1[0],$p1[1]) ; $interp,$long","\n";
                }
            }
        }
        $p0 = $p1;
    }
    return ($ctr & 1);
}