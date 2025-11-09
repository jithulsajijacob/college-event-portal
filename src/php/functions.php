<?php
function e($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function format_date($d)
{
    return $d ? date('M j, Y', strtotime($d)) : '';
}
function format_time($t)
{
    return $t ? date('g:i A', strtotime($t)) : '';
}
