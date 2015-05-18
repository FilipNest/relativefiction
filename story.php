<?php

require_once 'HTTP/Request2.php';

//Include API keys

include "secrets.php";

//Get Foursquare categories list once a day or if not set

if(true){

$request = new HTTP_Request2('https://api.foursquare.com/v2/venues/categories',
                             HTTP_Request2::METHOD_GET, array('use_brackets' => true));
$url = $request->getUrl();
$url->setQueryVariables(array(

    "client_id" => $foursquare['id'],
    "client_secret" => $foursquare['secret'],
    "v" => "20150516"
  
));

$response = $request->send()->getBody();

$response = json_decode($response, true)['response']['categories'];

$venues = array();

function traverse($object){
  
  global $venues;
    
  $venues[strtolower($object['name'])] = $object["id"];
  
  if(isset($object["categories"]) && count($object["categories"] > 0)){
   
    foreach($object["categories"] as $subcategory){
     
      traverse($subcategory);
      
    }
    
  }
    
};
  
foreach($response as $category){
  
  traverse($category);
  
}   
  
  $response = json_encode($venues);
    file_put_contents('venuecategories.json', $response);
  
};

$foursquare['venuecategories'] = json_decode(file_get_contents("venuecategories.json"));

//Check all data is present

$time = $_POST["time"];
$location = $_POST["location"];
$output = json_decode($_POST["text"]);

//Static variables first

//Create date elements

$day = date("l",$time);
$month = date("F",$time);
$year = date("Y", $time);
$hours12 = date("h", $time);
$hours24 = date("H", $time);
$hoursampm = date("A", $time);
$minutes = date("i", $time);

//Days

$output = str_replace("[day]",$day,$output);

//Months

$output = str_replace("[month]", $month, $output);

//Years

$output = str_replace("[year]", $year, $output);

//Hours (12) 

$output = str_replace("[hours12]",$hours12,$output);

//Hours (24)

$output = str_replace("[hours24]", $hours24, $output);

//Hours AM/PM

$output = str_replace("[hoursampm]", $hoursampm, $output);

//Minutes

$output = str_replace("[minutes]", $minutes, $output);


//Get list of dynamic variables in the text
  
preg_match_all("/\[([^\]]*)\]/", $output, $matches);

//Store matches in array

$variables = $matches[1];

$categoryids = array();

$placevariables = array();

foreach($variables as $variable){
 
  $name = explode("|",$variable)[0];
  
  if(count(explode("|",$variable)) > 0){
  $id = explode("|",$variable)[1];
  }
  
  if(isset($foursquare['venuecategories']->$name)){
    
    $placevariables[] = array(
      "tag" => $variable,
      "category" => $foursquare['venuecategories']->$name,
      "id" => $id
    );
    
    //Get category id
    
    $categoryids[] = $foursquare['venuecategories']->$name;
    
  }
  
}

//Get weather data for location

$request = new HTTP_Request2('http://api.openweathermap.org/data/2.5/weather',
                             HTTP_Request2::METHOD_GET, array('use_brackets' => true));
$url = $request->getUrl();
$url->setQueryVariables(array(

    "lat" => $location["latitude"],
    "lon" => $location["longitude"]

));

$weather = $request->send()->getBody();

$weather = json_decode($weather);

$weathercode = $weather->weather[0]->id;

//Translate weather codes to useable variables

$forecast = array(

"thunderstorm" => [200,201,202,210,211,212,221,230,231,232,960,961,962,900,901,902],
"rain" => [300,301,302,310,311,312,313,314,321,500,501,502,503,504,511,520,521,522,531],
"snow" => [600,601,602,611,612,615,616,620,621,622,906],
"clear" => [800,801],
"cloudy" => [802,803,804],
"calm" => [951,952,953],
"windy" => [954,955,956,957,958,959,960,905],
"extremeheat" => [904],
"extremecold" => [906]
  
);

//Country list

$countries = array
(
	'AF' => 'Afghanistan',
	'AX' => 'Aland Islands',
	'AL' => 'Albania',
	'DZ' => 'Algeria',
	'AS' => 'American Samoa',
	'AD' => 'Andorra',
	'AO' => 'Angola',
	'AI' => 'Anguilla',
	'AQ' => 'Antarctica',
	'AG' => 'Antigua And Barbuda',
	'AR' => 'Argentina',
	'AM' => 'Armenia',
	'AW' => 'Aruba',
	'AU' => 'Australia',
	'AT' => 'Austria',
	'AZ' => 'Azerbaijan',
	'BS' => 'Bahamas',
	'BH' => 'Bahrain',
	'BD' => 'Bangladesh',
	'BB' => 'Barbados',
	'BY' => 'Belarus',
	'BE' => 'Belgium',
	'BZ' => 'Belize',
	'BJ' => 'Benin',
	'BM' => 'Bermuda',
	'BT' => 'Bhutan',
	'BO' => 'Bolivia',
	'BA' => 'Bosnia And Herzegovina',
	'BW' => 'Botswana',
	'BV' => 'Bouvet Island',
	'BR' => 'Brazil',
	'IO' => 'British Indian Ocean Territory',
	'BN' => 'Brunei Darussalam',
	'BG' => 'Bulgaria',
	'BF' => 'Burkina Faso',
	'BI' => 'Burundi',
	'KH' => 'Cambodia',
	'CM' => 'Cameroon',
	'CA' => 'Canada',
	'CV' => 'Cape Verde',
	'KY' => 'Cayman Islands',
	'CF' => 'Central African Republic',
	'TD' => 'Chad',
	'CL' => 'Chile',
	'CN' => 'China',
	'CX' => 'Christmas Island',
	'CC' => 'Cocos (Keeling) Islands',
	'CO' => 'Colombia',
	'KM' => 'Comoros',
	'CG' => 'Congo',
	'CD' => 'Congo, Democratic Republic',
	'CK' => 'Cook Islands',
	'CR' => 'Costa Rica',
	'CI' => 'Cote D\'Ivoire',
	'HR' => 'Croatia',
	'CU' => 'Cuba',
	'CY' => 'Cyprus',
	'CZ' => 'Czech Republic',
	'DK' => 'Denmark',
	'DJ' => 'Djibouti',
	'DM' => 'Dominica',
	'DO' => 'Dominican Republic',
	'EC' => 'Ecuador',
	'EG' => 'Egypt',
	'SV' => 'El Salvador',
	'GQ' => 'Equatorial Guinea',
	'ER' => 'Eritrea',
	'EE' => 'Estonia',
	'ET' => 'Ethiopia',
	'FK' => 'Falkland Islands (Malvinas)',
	'FO' => 'Faroe Islands',
	'FJ' => 'Fiji',
	'FI' => 'Finland',
	'FR' => 'France',
	'GF' => 'French Guiana',
	'PF' => 'French Polynesia',
	'TF' => 'French Southern Territories',
	'GA' => 'Gabon',
	'GM' => 'Gambia',
	'GE' => 'Georgia',
	'DE' => 'Germany',
	'GH' => 'Ghana',
	'GI' => 'Gibraltar',
	'GR' => 'Greece',
	'GL' => 'Greenland',
	'GD' => 'Grenada',
	'GP' => 'Guadeloupe',
	'GU' => 'Guam',
	'GT' => 'Guatemala',
	'GG' => 'Guernsey',
	'GN' => 'Guinea',
	'GW' => 'Guinea-Bissau',
	'GY' => 'Guyana',
	'HT' => 'Haiti',
	'HM' => 'Heard Island & Mcdonald Islands',
	'VA' => 'Holy See (Vatican City State)',
	'HN' => 'Honduras',
	'HK' => 'Hong Kong',
	'HU' => 'Hungary',
	'IS' => 'Iceland',
	'IN' => 'India',
	'ID' => 'Indonesia',
	'IR' => 'Iran, Islamic Republic Of',
	'IQ' => 'Iraq',
	'IE' => 'Ireland',
	'IM' => 'Isle Of Man',
	'IL' => 'Israel',
	'IT' => 'Italy',
	'JM' => 'Jamaica',
	'JP' => 'Japan',
	'JE' => 'Jersey',
	'JO' => 'Jordan',
	'KZ' => 'Kazakhstan',
	'KE' => 'Kenya',
	'KI' => 'Kiribati',
	'KR' => 'Korea',
	'KW' => 'Kuwait',
	'KG' => 'Kyrgyzstan',
	'LA' => 'Lao People\'s Democratic Republic',
	'LV' => 'Latvia',
	'LB' => 'Lebanon',
	'LS' => 'Lesotho',
	'LR' => 'Liberia',
	'LY' => 'Libyan Arab Jamahiriya',
	'LI' => 'Liechtenstein',
	'LT' => 'Lithuania',
	'LU' => 'Luxembourg',
	'MO' => 'Macao',
	'MK' => 'Macedonia',
	'MG' => 'Madagascar',
	'MW' => 'Malawi',
	'MY' => 'Malaysia',
	'MV' => 'Maldives',
	'ML' => 'Mali',
	'MT' => 'Malta',
	'MH' => 'Marshall Islands',
	'MQ' => 'Martinique',
	'MR' => 'Mauritania',
	'MU' => 'Mauritius',
	'YT' => 'Mayotte',
	'MX' => 'Mexico',
	'FM' => 'Micronesia, Federated States Of',
	'MD' => 'Moldova',
	'MC' => 'Monaco',
	'MN' => 'Mongolia',
	'ME' => 'Montenegro',
	'MS' => 'Montserrat',
	'MA' => 'Morocco',
	'MZ' => 'Mozambique',
	'MM' => 'Myanmar',
	'NA' => 'Namibia',
	'NR' => 'Nauru',
	'NP' => 'Nepal',
	'NL' => 'Netherlands',
	'AN' => 'Netherlands Antilles',
	'NC' => 'New Caledonia',
	'NZ' => 'New Zealand',
	'NI' => 'Nicaragua',
	'NE' => 'Niger',
	'NG' => 'Nigeria',
	'NU' => 'Niue',
	'NF' => 'Norfolk Island',
	'MP' => 'Northern Mariana Islands',
	'NO' => 'Norway',
	'OM' => 'Oman',
	'PK' => 'Pakistan',
	'PW' => 'Palau',
	'PS' => 'Palestinian Territory, Occupied',
	'PA' => 'Panama',
	'PG' => 'Papua New Guinea',
	'PY' => 'Paraguay',
	'PE' => 'Peru',
	'PH' => 'Philippines',
	'PN' => 'Pitcairn',
	'PL' => 'Poland',
	'PT' => 'Portugal',
	'PR' => 'Puerto Rico',
	'QA' => 'Qatar',
	'RE' => 'Reunion',
	'RO' => 'Romania',
	'RU' => 'Russian Federation',
	'RW' => 'Rwanda',
	'BL' => 'Saint Barthelemy',
	'SH' => 'Saint Helena',
	'KN' => 'Saint Kitts And Nevis',
	'LC' => 'Saint Lucia',
	'MF' => 'Saint Martin',
	'PM' => 'Saint Pierre And Miquelon',
	'VC' => 'Saint Vincent And Grenadines',
	'WS' => 'Samoa',
	'SM' => 'San Marino',
	'ST' => 'Sao Tome And Principe',
	'SA' => 'Saudi Arabia',
	'SN' => 'Senegal',
	'RS' => 'Serbia',
	'SC' => 'Seychelles',
	'SL' => 'Sierra Leone',
	'SG' => 'Singapore',
	'SK' => 'Slovakia',
	'SI' => 'Slovenia',
	'SB' => 'Solomon Islands',
	'SO' => 'Somalia',
	'ZA' => 'South Africa',
	'GS' => 'South Georgia And Sandwich Isl.',
	'ES' => 'Spain',
	'LK' => 'Sri Lanka',
	'SD' => 'Sudan',
	'SR' => 'Suriname',
	'SJ' => 'Svalbard And Jan Mayen',
	'SZ' => 'Swaziland',
	'SE' => 'Sweden',
	'CH' => 'Switzerland',
	'SY' => 'Syrian Arab Republic',
	'TW' => 'Taiwan',
	'TJ' => 'Tajikistan',
	'TZ' => 'Tanzania',
	'TH' => 'Thailand',
	'TL' => 'Timor-Leste',
	'TG' => 'Togo',
	'TK' => 'Tokelau',
	'TO' => 'Tonga',
	'TT' => 'Trinidad And Tobago',
	'TN' => 'Tunisia',
	'TR' => 'Turkey',
	'TM' => 'Turkmenistan',
	'TC' => 'Turks And Caicos Islands',
	'TV' => 'Tuvalu',
	'UG' => 'Uganda',
	'UA' => 'Ukraine',
	'AE' => 'United Arab Emirates',
	'GB' => 'United Kingdom',
	'US' => 'United States',
	'UM' => 'United States Outlying Islands',
	'UY' => 'Uruguay',
	'UZ' => 'Uzbekistan',
	'VU' => 'Vanuatu',
	'VE' => 'Venezuela',
	'VN' => 'Viet Nam',
	'VG' => 'Virgin Islands, British',
	'VI' => 'Virgin Islands, U.S.',
	'WF' => 'Wallis And Futuna',
	'EH' => 'Western Sahara',
	'YE' => 'Yemen',
	'ZM' => 'Zambia',
	'ZW' => 'Zimbabwe',
);


$country = $countries[$weather->sys->country];

print_r($country);
print "<br />";

foreach ($forecast as $name => $condition){
  
  if(in_array($weathercode,$condition)){

  $weather = $name;

  }
  
}
  

//Make bulk request for all Foursquare categories in text

$request = new HTTP_Request2('https://api.foursquare.com/v2/venues/search',
                             HTTP_Request2::METHOD_GET, array('use_brackets' => true));
$url = $request->getUrl();
$url->setQueryVariables(array(

    "client_id" => $foursquare['id'],
    "client_secret" => $foursquare['secret'],
  "limit" => 50,
    "v" => "20150516",
    "ll" => $location["latitude"].",".$location["longitude"],
  "categoryId" => implode(",",$categoryids)

));

$places = $request->send()->getBody();

$places = json_decode($places)->response->venues;

//Sort into array

$fetchedvenues = array();

foreach ($places as $place){
  
  foreach ($place->categories as $category){
      
  if(!isset($fetchedvenues[$category->id])){
  
    $fetchedvenues[$category->id] = array();
    
  }
  
     $fetchedvenues[$category->id][] = $place;
    
  }
  
};

//Loop over variables to swap in places

foreach($placevariables as $place){
  
  $tag = $place["tag"];
  $category = $place["category"];
  $id = $place["id"] - 1;
   
  if(isset($fetchedvenues[$place["category"]]) && isset($fetchedvenues[$place["category"]][$id])){
   
    $venue = $fetchedvenues[$place["category"]][$id]->name;
    
    $output = str_replace("[".$tag."]", $venue, $output);
    
  }
  
}

//Finally print the output

print $output;

?>