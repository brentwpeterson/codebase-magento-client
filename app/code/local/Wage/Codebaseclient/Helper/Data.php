<?php
/**
 *
 * @author Wagento
 */
class Wage_Codebaseclient_Helper_Data extends Mage_Core_Helper_Abstract {
    public function convertToHoursMins($time, $format = '%d:%d') {
        settype($time, 'integer');
        if ($time < 1) {
            return ;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public function convertHoursToMinutes($hours)
    {
        if (strstr($hours, ':'))
        {
            # Split hours and minutes.
            $separatedData = split(':', $hours);

            $minutesInHours    = $separatedData[0] * 60;
            $minutesInDecimals = $separatedData[1];

            $totalMinutes = $minutesInHours + $minutesInDecimals;
        }
        else
        {
            $totalMinutes = $hours * 60;
        }

        return $totalMinutes;
    }   
}
