<?php

namespace App\Traits;

trait Geocoding
{
    /**
     * Hàm tìm kiếm, tỉnh, thành phố, quận, huyện dựa vào trường text
     */
    public function getAddressFromText($address, $provinces, $districts)
    {
        $province = null;
        $district = null;

        foreach($provinces as $item) {
            if (strpos($address, $item->name) !== false) {
                $province = $item;
                break;
            }
        }

        foreach($districts as $item) {
            if (strpos($address, $item->name) !== false) {
                $district = $item;
                if(empty($province)) {
                    $province = $provinces->firstWhere('id', $district->province_id);
                }
                break;
            }
        }

        return [
            'province' => $province,
            'district' => $district,
        ];
    }

    public function getLatLonByAddressText($address, $curl = false) {
        $data2 = [];
        $lat = null;
        $long = null;
        $full_name = null;
        $result = file_get_contents('https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.urlencode($address).'&types=geocode&language=vi&key=AIzaSyCr3gClMFwEcQL1anGqzoCNoi9js26X2Pg');
        // $result = file_get_contents('http://192.168.1.101:3000/geocoding/autocomplete?q='.urlencode($address).'&fbclid=IwAR2YX6De_ZCpCIe24Vqcdi0emUNJAYHbNRABf96JdWrC9E5_vplmVBnVzjs');

        foreach (json_decode($result)->predictions as $item){
            $result2 = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$item->place_id.'&key=AIzaSyCr3gClMFwEcQL1anGqzoCNoi9js26X2Pg');
            //$result2 = file_get_contents('http://192.168.1.101:3000/geocoding/details?q='.$item->place_id.'&fbclid=IwAR2YX6De_ZCpCIe24Vqcdi0emUNJAYHbNRABf96JdWrC9E5_vplmVBnVzjs');
            $result2 = json_decode($result2);
            //$coordinates = json_decode($item->coordinate)->coordinates;
            //$data2[] =['full_name'=> $item->full_name, 'lat'=>$coordinates[1],'long'=>$coordinates[0]];
            $data2[] =['full_name'=> $item->description, 'lat'=>$result2->result->geometry->location->lat,'long'=>$result2->result->geometry->location->lng];
        }
        return $data2;
    }

    public function getFullAddress($address, $provinces, $districts) {
        $latLon = $this->getLatLonByAddressText($address);
        $districtProvince = $this->getAddressFromText($address, $provinces, $districts);
        return [
            'lat' => $latLon['lat'],
            'long' => $latLon['long'],
            'province' => $districtProvince['province'],
            'district' => $districtProvince['district']
        ];
    }

    private function getGoogleGeocodingApi($requestFieldsString, $curl = false)
    {
        $googleGeocodeApiUrl = 'https://maps.googleapis.com/maps/api/geocode/json?';
        if ($curl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $googleGeocodeApiUrl . $requestFieldsString);
            $result = curl_exec($ch);
            curl_close($ch);
        } else
            $result = file_get_contents($googleGeocodeApiUrl . $requestFieldsString);
        return $result;
    }

    function getAddressByLatLon($lat, $lon) {
        $result = [];
        $data = [];
        $latLon = doubleval($lat) . ',' . doubleval($lon);

        $requestFields = [
            'latlng' => urlencode($latLon),
            'key' => 'AIzaSyA6oWHVnZxDlvG7naTBTYKBWRQQQLDwRko'
        ];
        $requestFieldsString = '';
        foreach ($requestFields as $key => $value) {
            $requestFieldsString .= $key . '=' . $value . '&';
        }
        rtrim($requestFieldsString, '&');

        try {
            $data = json_decode(self::getGoogleGeocodingApi($requestFieldsString, false), true);
        } catch (\Exception $exception) {
            # log error
        }

        if (!empty($data['results']) && isset($data['results'][0])) {
            $transformedResult = self::transform($data['results'][0]);
            if (!empty($transformedResult)) {
                $result[] = $transformedResult;
            }
        }

        return $result;
    }

    public static function transform($geocodeResult)
    {
        $transformedResult = [];
        $transformedResult['district'] = "";
        $transformedResult['state_province'] = "";
        $transformedResult['country'] = "";
        $transformedResult['street'] = "";

        if (isset($geocodeResult['address_components'])) {
            $addressComponents = $geocodeResult['address_components'];
            $transformedResult['street'] = '';
            $streetSuffix = [];

            foreach ($addressComponents as $addressComponent) {
                if (in_array("political", $addressComponent['types'])) {
                    if (in_array("country", $addressComponent['types'])) {
                        $transformedResult['country'] = $addressComponent['long_name'];
                    } elseif (in_array("administrative_area_level_1", $addressComponent['types'])) {
                        $transformedResult['state_province'] = $addressComponent['long_name'];
                    } elseif (in_array("administrative_area_level_2", $addressComponent['types'])) {
                        $transformedResult['district'] = $addressComponent['long_name'];
                    } elseif (in_array("sublocality", $addressComponent['types'])) {
                        $streetSuffix[] = $addressComponent['long_name'];
                    }
                } else {
                    $transformedResult['street'] .= $addressComponent['long_name'] . ', ';
                }
            }
            if (count($streetSuffix) > 0)
                $transformedResult['street'] .= join(', ', $streetSuffix);
            if (substr($transformedResult['street'], strlen($transformedResult['street']) - 3) == ', ') {
                $transformedResult['street'] = substr($transformedResult['street'], 0, strlen($transformedResult['street']) - 3);
            }
        }

        if (isset($geocodeResult['name']) && isset($geocodeResult['vicinity'])) {
            $transformedResult['street'] = $geocodeResult['vicinity'];
        }

        return $transformedResult;
    }
}
