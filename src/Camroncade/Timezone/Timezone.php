<?php namespace Camroncade\Timezone;

use DateTime;
use DateTimeZone;

class Timezone {

	public function selectForm($selected = null, $placeholder = null, array $selectAttributes = [], array $optionAttributes = [] )
	{
		$selectAttributesString = '';
		foreach ($selectAttributes as $key => $value)
		{
			$selectAttributesString = $selectAttributesString . " " . $key . "='" . $value ."'";
		}

		$optionAttributesString = '';
		foreach ($optionAttributes as $key => $value)
		{
			$optionAttributesString = $optionAttributesString . " " . $key . "='" . $value ."'";
		}

		$string = "<select". $selectAttributesString .">\n";

		if (isset($placeholder) && (empty($selected)))
		{
		     $placeholder = "<option value='' disabled selected>{$placeholder}</option>";
		}
		else
		{
		      $placeholder = null;
		}

		$string = $string . $placeholder;
		foreach ($this->timezoneList() as $value => $name)
		{
			if ($selected == $value) {
				$selectedString = "selected='" . $value . "'";
			} else {
				$selectedString = '';
			}

			$string = $string . "<option value='" . $value . "'" . $optionAttributesString . " " . $selectedString . ">" . $name . "</option>\n";
		}
		$string = $string . "</select>";

		return $string;
	}

	public function convertFromUTC($timestamp, $timezone, $format = 'Y-m-d H:i:s')
	{
        $date = new DateTime($timestamp, new DateTimeZone('UTC'));

        $date->setTimezone(new DateTimeZone($timezone));

        return $date->format($format);
    } 

    public function convertToUTC($timestamp, $timezone, $format = 'Y-m-d H:i:s')
    {
    	$date = new DateTime($timestamp, new DateTimeZone($timezone));

        $date->setTimezone(new DateTimeZone('UTC'));

        return $date->format($format);
    }

    public function timezoneList() {
        static $timezones = null;

        if ($timezones === null) {
            $timezones = [];
            $offsets = [];
            $now = new \DateTime('now', new \DateTimeZone('UTC'));

            foreach (\DateTimeZone::listIdentifiers() as $timezone) {
                $now->setTimezone(new \DateTimeZone($timezone));
                $offsets[] = $offset = $now->getOffset();
                $timezones[$timezone] = '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone);
            }

            array_multisort($offsets, $timezones);
        }

        return $timezones;
    }

    protected function format_GMT_offset($offset) {
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    protected function format_timezone_name($name) {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);
        return $name;
    }

}
