<?php

namespace Iml\Loaders;

class CitySorter
{
    const MOSCOW_NAME = 'МОСКВА Г.';
    const SPB_NAME = 'САНКТ-ПЕТЕРБУРГ Г.';

    private function cmp($a, $b)
    {

        $isMoscowA = mb_stripos($a['city'], 'МОСКВА', 0,  'UTF-8') !== false;
        $isSpbA = mb_stripos($a['city'], 'САНКТ-ПЕТЕРБУРГ',0, 'UTF-8') !== false;


        $isMoscowB = mb_stripos($b['city'], 'МОСКВА',0, 'UTF-8') !== false;
        $isSpbB = mb_stripos($b['city'], 'САНКТ-ПЕТЕРБУРГ',0, 'UTF-8') !== false;

        if ($isMoscowA && $isSpbB) {
            return -1;
        } else if ($isMoscowB && $isSpbA) {
            return 1;
        } else if ($isMoscowA || $isSpbA) {
            return -1;
        } else if ($isMoscowB || $isSpbB) {
            return 1;
        } else {
            return strcmp($a['city'], $b['city']);
        }

    }

    public function sort($values)
    {
        uasort($values, array($this, 'cmp'));
        return $values;

    }

}
