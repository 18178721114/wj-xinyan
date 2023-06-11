<?php

namespace Air\Libs\Base;

use \Air\Libs\Base\Utilities;

class GPSTools
{
    const AK = 'sHhIyEwjoMG5dL64iGl66LW0Mwn5vXLN';
    const OUTPUT = 'json';
    const COORDTYPE = 'wgs84ll';
    const TRACKCOORDTYPE = 'wgs84';
    const URL = 'http://api.map.baidu.com/reverse_geocoding/v3/';
    const SERVICEID = '24755218';

    public static function getGeocoding($lat, $lng)
    {
        $requireUrl = self::URL . '?ak=' . self::AK . '&output=' . self::OUTPUT . '&coordtype=' . self::COORDTYPE . '&location=' . $lat . ',' . $lng;
        return Utilities::curl($requireUrl);
    }

    public static function getLatLng($address)
    {
        $requireUrl = "https://api.map.baidu.com/geocoding/v3/?address={$address}&output=json&ak=" . self::AK;
        $result = Utilities::curl($requireUrl);
        if ($result['status'] == 0) {
            return ['lng' => $result['result']['location']['lng'], 'lat' => $result['result']['location']['lat']];
        }
        return ['lng' => -1, 'lat' => -1];
    }


    public static function baiduApiTrackAddPoint($entityName, $latitude, $longitude, $locTime)
    {

        $trackApi = "http://yingyan.baidu.com/api/v3/track/addpoint";
        $params = [
            'ak' => self::AK,
            'service_id' => self::SERVICEID,
            'entity_name' => $entityName,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'loc_time' => $locTime,
            'coord_type_input' => self::TRACKCOORDTYPE
        ];
        return Utilities::curl($trackApi, $params, $options = array('is_post' => 1));
    }
}
