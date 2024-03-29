<?php

# function for fetching the webpage and parse data
function fetchPage($kodzon,$tahun,$bulan)
{
	$url = "https://www.e-solat.gov.my/index.php?r=esolatApi/takwimsolat&period=duration&zone=".$kodzon;

		# data for POST request
		$dates = getDurationDate($bulan, $tahun);
    $postdata = http_build_query(
        array(
            'datestart' => $dates['start'],
            'dateend' => $dates['end'],
        )
    );

    # cURL also have more options and customizable
    $ch = curl_init(); # initialize curl object
    curl_setopt($ch, CURLOPT_URL, $url); # set url
    curl_setopt($ch, CURLOPT_POST, 1); # set option for POST data
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); # set post data array
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # receive server response
    $result = curl_exec($ch); # execute curl, fetch webpage content
    $httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # receive http response status
    curl_close($ch);  # close curl

	$arrData = array();
	$waktusolat = json_decode($result, true);

	if($waktusolat!=null) {
	if(is_array($waktusolat['prayerTime']) && count($waktusolat['prayerTime']) > 0) {
		foreach ($waktusolat['prayerTime'] as $waktu) {

			$arrData[]= array(
				'hijri' => $waktu['hijri'],
				'date' => date("d-m-Y", myStrtotime($waktu['date'])),
				'day' => $waktu['day'],
				'imsak' => convertTime($waktu['imsak'],'imsak'),
				'subuh' => convertTime($waktu['fajr'],'subuh'),
				'syuruk' => convertTime($waktu['syuruk'],'syuruk'),
				'zohor' => convertTime($waktu['dhuhr'],'zohor'),
				'asar' => convertTime($waktu['asr'],'asar'),
				'maghrib' => convertTime($waktu['maghrib'],'maghrib'),
				'isyak' => convertTime($waktu['isha'],'isyak'),
			);
		}
	}
}

    return $arrData; # return array data
}


# fetch for all 12 months for the zone
if(isset($_GET['zon']) && isset($_GET['tahun']))
{
	$kodzon = $_GET['zon'];
	$tahun = $_GET['tahun'];

	$zone = fetchZone();


	$arrData = array();

	for($i=1;$i<=12;$i++)
	{
		$curr_arr = $arrData;
		$d = fetchPage($kodzon,$tahun,$i);
		$arrData = array_merge($curr_arr,$d);
	}

	$data = new stdClass();
	$data->zone = strtoupper($kodzon);
	$data->start = "01-01-".$tahun;
	$data->end = "31-12-".$tahun;
	$data->locations = $zone[strtoupper($kodzon)];
	
	$data->prayer_times = $arrData;

	# print JSON data
	//json_encode($data);
	# save JSON data to local
	$json = json_encode($data);
	echo $json;
}

# Get All Zone 12 months
if(isset($_GET['tahun']))
{
	$zones = fetchZone();
	foreach ($zones as $key => $value) {
	$kodzon = $key;
	$tahun = $_GET['tahun'];

	$zone = fetchZone();


	$arrData = array();

	for($i=1;$i<=12;$i++)
	{
		$curr_arr = $arrData;
		$d = fetchPage($kodzon,$tahun,$i);
		$arrData = array_merge($curr_arr,$d);
	}

	$data = new stdClass();
	$data->zone = strtoupper($kodzon);
	$data->start = "01-01-".$tahun;
	$data->end = "31-12-".$tahun;
	$data->locations = $zone[strtoupper($kodzon)];
	
	$data->prayer_times = $arrData;

	# print JSON data
	//json_encode($data);
	# save JSON data to local
	$json = json_encode($data);
	$filename = $kodzon . ".json";
	$file = fopen($filename,"w");
	fwrite($file,$json);
	fclose($file);
}
}

# fetch for all 12 months for the zone
if(!isset($_GET['zon']) && isset($_GET['tahun']))
{
	$tahun = $_GET['tahun'];

	$zone = fetchZone();

	$zoneList = [
		"JHR01",
		"JHR02",
		"JHR03",
		"JHR04",
		"KDH01",
		"KDH02",
		"KDH03",
		"KDH04",
		"KDH05",
		"KDH06",
		"KDH07",
		"KTN01",
		"KTN03",
		"MLK01",
		"NGS01",
		"NGS02",
		"PHG01",
		"PHG02",
		"PHG03",
		"PHG04",
		"PHG05",
		"PHG06",
		"PLS01",
		"PNG01",
		"PRK01",
		"PRK02",
		"PRK03",
		"PRK04",
		"PRK05",
		"PRK06",
		"PRK07",
		"SBH01",
		"SBH02",
		"SBH03",
		"SBH04",
		"SBH05",
		"SBH06",
		"SBH07",
		"SBH08",
		"SBH09",
		"SGR01",
		"SGR02",
		"SGR03",
		"SWK01",
		"SWK02",
		"SWK03",
		"SWK04",
		"SWK05",
		"SWK06",
		"SWK07",
		"SWK08",
		"SWK09",
		"TRG01",
		"TRG02",
		"TRG03",
		"TRG04",
		"WLY01",
		"WLY02",
	];


	$prayerData = array();
	for($a=0;$a<count($zoneList);$a++) {
		$kodzon = $zoneList[$a];

		for($i=1;$i<=12;$i++)
		{
			$d = fetchPage($kodzon,$tahun,$i);
			for($f=0;$f<count($d);$f++) {

				$prayer = $d[$f];
				$data = new stdClass();
				$data->zoneCode = strtoupper($kodzon);
				$data->date = date("Y-m-d", myStrtotime($prayer['date']));
				$data->dayName = $prayer['day'];
				$data->hijriDate = $prayer['hijri'];
				$data->imsak = $prayer['imsak'];
				$data->subuh = $prayer['subuh'];
				$data->syuruk = $prayer['syuruk'];
				$data->zohor = $prayer['zohor'];
				$data->asar = $prayer['asar'];
				$data->maghrib = $prayer['maghrib'];
				$data->isyak = $prayer['isyak'];

				$location = $zone[strtoupper($kodzon)];

				$data->zoneLocation = implode(",",$location);

				$prayerData[] = $data;

			}	
		}

	}
	

	# print JSON data
	echo json_encode($prayerData);
}

# if no parameters is supplied, show usage message
if(!isset($_GET['zon']) && !isset($_GET['tahun']))
{
	?>
		<p>
			Fetch data for a year <br>
			example: http://localhost/<font color="blue">solat.php?zon=<font color="red">PLS01</font>&tahun=<font color="red">2020</font></font> ,  <br>
			where "<font color="red">PLS01</font>" is the zone code, <font color="red">2020</font> is the year<br>
		</p>
	<?php
}


function myStrtotime($date_string)
{
	 $convertDate = array('jan'=>'jan','feb'=>'feb','mac'=>'march','apr'=>'apr','mei'=>'may','jun'=>'jun','jul'=>'jul','ogos'=>'aug','sep'=>'sep','okt'=>'oct','nov'=>'nov','dis'=> 'dec');
	 return strtotime(strtr(strtolower($date_string), $convertDate));
}

function getDurationDate($month, $year)
{
	$month = str_pad($month,2,'0',STR_PAD_LEFT);
	$startdate = $year.'-'.$month.'-'.'01';
	$enddate = $year.'-'.$month.'-'.date("t", strtotime(date("F", mktime(0, 0, 0, $month, 10))));

	return array(
		'start' => $startdate,
		'end' => $enddate
	);
}

// Function to convert the time
// User reported some data is incorrect. AM instead of PM
// only subuh, imsak and syurk should have AM in Malaysia
function convertTime($time, $prayer)
{
    // replace separator
    $time = str_replace(".", ":", $time);
    // convert 24h to 12h
    $newtime = date('h:i a', strtotime($time));
    // include a.m. or p.m. prefix
    //$newtime .= explode(':', $time)[0] <= 12 ? ' am' : ' pm';
	//$newtime .= $prayer == 'imsak' || $prayer == 'subuh' || $prayer == 'syuruk' ? ' am':' pm';
    return $newtime;
}

function fetchZone() {
	$url = "https://www.e-solat.gov.my/index.php?siteId=24&pageId=24";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$patern = '#<select id="inputZone" class="form-control">([\w\W]*?)</select>#';
	preg_match_all($patern, $result, $result);

	$patern = '#<optgroup([\w\W]*?)</optgroup>#';
	preg_match_all($patern, $result[0][0], $result);

	$stateJson = array();
	foreach ($result[0] as $options) {

		// get state name
		$patern = '#label="([\w\W]*?)"#';
		preg_match_all($patern, $options, $statearr);
		$state  = $statearr[1][0];

		// get zones
		$patern = '#<option([\w\W]*?)</option>#';
		preg_match_all($patern, $options, $zonearr);

		$zonJson = array();
		foreach ($zonearr[0] as $zoneoption) {
			// get zone code
			$patern = "#value='([\w\W]*?)'#";
			preg_match_all($patern, $zoneoption, $zonecodearr);
			$zonecode = $zonecodearr[1][0];
			$zonename = (explode(" - ", strip_tags($zoneoption)))[1];

			// split zone name by ","
			$zones = explode(",",trim(strip_tags($zonename)));
			$zonJson[$zonecode] = $zones;
		}

		$original = $stateJson;
		$stateJson = array_merge($original,$zonJson);
	}

	return $stateJson;

}

?>
