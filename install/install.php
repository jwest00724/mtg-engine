<?php
include(__DIR__ . '/header.php');
define('MTG_ENABLE', true);
function error($msg) {
	exit("<div class='notification notification-error'><i class='fa fa-times-circle'></i><p>".$msg."</p></div>");
}
function success($msg) {
	echo "<div class='notification notification-success'><i class='fa fa-check-circle'></i><p>".$msg."</p></div>";
}
function info($msg) {
	echo "<div class='notification notification-info'><i class='fa fa-info-circle'></i><p>".$msg."</p></div>";
}
function warning($msg) {
	echo "<div class='notification notification-secondary'><i class='fa fa-secondary-circle'></i><p>".$msg."</p></div>";
}
if(!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
$timezones = array(
	'America/Adak' => 'America/Adak (GMT-10:00)',
	'America/Atka' => 'America/Atka (GMT-10:00)',
	'America/Anchorage' => 'America/Anchorage (GMT-9:00)',
	'America/Juneau' => 'America/Juneau (GMT-9:00)',
	'America/Nome' => 'America/Nome (GMT-9:00)',
	'America/Yakutat' => 'America/Yakutat (GMT-9:00)',
	'America/Dawson' => 'America/Dawson (GMT-8:00)',
	'America/Ensenada' => 'America/Ensenada (GMT-8:00)',
	'America/Los_Angeles' => 'America/Los Angeles (GMT-8:00)',
	'America/Tijuana' => 'America/Tijuana (GMT-8:00)',
	'America/Vancouver' => 'America/Vancouver (GMT-8:00)',
	'America/Whitehorse' => 'America/Whitehorse (GMT-8:00)',
	'Canada/Pacific' => 'Canada/Pacific (GMT-8:00)',
	'Canada/Yukon' => 'Canada/Yukon (GMT-8:00)',
	'Mexico/BajaNorte' => 'Mexico/BajaNorte (GMT-8:00)',
	'America/Boise' => 'America/Boise (GMT-7:00)',
	'America/Cambridge_Bay' => 'America/Cambridge_Bay (GMT-7:00)',
	'America/Chihuahua' => 'America/Chihuahua (GMT-7:00)',
	'America/Dawson_Creek' => 'America/Dawson_Creek (GMT-7:00)',
	'America/Denver' => 'America/Denver (GMT-7:00)',
	'America/Edmonton' => 'America/Edmonton (GMT-7:00)',
	'America/Hermosillo' => 'America/Hermosillo (GMT-7:00)',
	'America/Inuvik' => 'America/Inuvik (GMT-7:00)',
	'America/Mazatlan' => 'America/Mazatlan (GMT-7:00)',
	'America/Phoenix' => 'America/Phoenix (GMT-7:00)',
	'America/Shiprock' => 'America/Shiprock (GMT-7:00)',
	'America/Yellowknife' => 'America/Yellowknife (GMT-7:00)',
	'Canada/Mountain' => 'Canada/Mountain (GMT-7:00)',
	'Mexico/BajaSur' => 'Mexico/BajaSur (GMT-7:00)',
	'America/Belize' => 'America/Belize (GMT-6:00)',
	'America/Cancun' => 'America/Cancun (GMT-6:00)',
	'America/Chicago' => 'America/Chicago (GMT-6:00)',
	'America/Costa_Rica' => 'America/Costa_Rica (GMT-6:00)',
	'America/El_Salvador' => 'America/El_Salvador (GMT-6:00)',
	'America/Guatemala' => 'America/Guatemala (GMT-6:00)',
	'America/Knox_IN' => 'America/Knox_IN (GMT-6:00)',
	'America/Managua' => 'America/Managua (GMT-6:00)',
	'America/Menominee' => 'America/Menominee (GMT-6:00)',
	'America/Merida' => 'America/Merida (GMT-6:00)',
	'America/Mexico_City' => 'America/Mexico_City (GMT-6:00)',
	'America/Monterrey' => 'America/Monterrey (GMT-6:00)',
	'America/Rainy_River' => 'America/Rainy_River (GMT-6:00)',
	'America/Rankin_Inlet' => 'America/Rankin_Inlet (GMT-6:00)',
	'America/Regina' => 'America/Regina (GMT-6:00)',
	'America/Swift_Current' => 'America/Swift_Current (GMT-6:00)',
	'America/Tegucigalpa' => 'America/Tegucigalpa (GMT-6:00)',
	'America/Winnipeg' => 'America/Winnipeg (GMT-6:00)',
	'Canada/Central' => 'Canada/Central (GMT-6:00)',
	'Canada/East-Saskatchewan' => 'Canada/East-Saskatchewan (GMT-6:00)',
	'Canada/Saskatchewan' => 'Canada/Saskatchewan (GMT-6:00)',
	'Chile/EasterIsland' => 'Chile/EasterIsland (GMT-6:00)',
	'Mexico/General' => 'Mexico/General (GMT-6:00)',
	'America/Atikokan' => 'America/Atikokan (GMT-5:00)',
	'America/Bogota' => 'America/Bogota (GMT-5:00)',
	'America/Cayman' => 'America/Cayman (GMT-5:00)',
	'America/Coral_Harbour' => 'America/Coral_Harbour (GMT-5:00)',
	'America/Detroit' => 'America/Detroit (GMT-5:00)',
	'America/Fort_Wayne' => 'America/Fort_Wayne (GMT-5:00)',
	'America/Grand_Turk' => 'America/Grand_Turk (GMT-5:00)',
	'America/Guayaquil' => 'America/Guayaquil (GMT-5:00)',
	'America/Havana' => 'America/Havana (GMT-5:00)',
	'America/Indianapolis' => 'America/Indianapolis (GMT-5:00)',
	'America/Iqaluit' => 'America/Iqaluit (GMT-5:00)',
	'America/Jamaica' => 'America/Jamaica (GMT-5:00)',
	'America/Lima' => 'America/Lima (GMT-5:00)',
	'America/Louisville' => 'America/Louisville (GMT-5:00)',
	'America/Montreal' => 'America/Montreal (GMT-5:00)',
	'America/Nassau' => 'America/Nassau (GMT-5:00)',
	'America/New_York' => 'America/New_York (GMT-5:00)',
	'America/Nipigon' => 'America/Nipigon (GMT-5:00)',
	'America/Panama' => 'America/Panama (GMT-5:00)',
	'America/Pangnirtung' => 'America/Pangnirtung (GMT-5:00)',
	'America/Port-au-Prince' => 'America/Port-au-Prince (GMT-5:00)',
	'America/Resolute' => 'America/Resolute (GMT-5:00)',
	'America/Thunder_Bay' => 'America/Thunder_Bay (GMT-5:00)',
	'America/Toronto' => 'America/Toronto (GMT-5:00)',
	'Canada/Eastern' => 'Canada/Eastern (GMT-5:00)',
	'America/Caracas' => 'America/Caracas (GMT-4:-30)',
	'America/Anguilla' => 'America/Anguilla (GMT-4:00)',
	'America/Antigua' => 'America/Antigua (GMT-4:00)',
	'America/Aruba' => 'America/Aruba (GMT-4:00)',
	'America/Asuncion' => 'America/Asuncion (GMT-4:00)',
	'America/Barbados' => 'America/Barbados (GMT-4:00)',
	'America/Blanc-Sablon' => 'America/Blanc-Sablon (GMT-4:00)',
	'America/Boa_Vista' => 'America/Boa_Vista (GMT-4:00)',
	'America/Campo_Grande' => 'America/Campo_Grande (GMT-4:00)',
	'America/Cuiaba' => 'America/Cuiaba (GMT-4:00)',
	'America/Curacao' => 'America/Curacao (GMT-4:00)',
	'America/Dominica' => 'America/Dominica (GMT-4:00)',
	'America/Eirunepe' => 'America/Eirunepe (GMT-4:00)',
	'America/Glace_Bay' => 'America/Glace_Bay (GMT-4:00)',
	'America/Goose_Bay' => 'America/Goose_Bay (GMT-4:00)',
	'America/Grenada' => 'America/Grenada (GMT-4:00)',
	'America/Guadeloupe' => 'America/Guadeloupe (GMT-4:00)',
	'America/Guyana' => 'America/Guyana (GMT-4:00)',
	'America/Halifax' => 'America/Halifax (GMT-4:00)',
	'America/La_Paz' => 'America/La_Paz (GMT-4:00)',
	'America/Manaus' => 'America/Manaus (GMT-4:00)',
	'America/Marigot' => 'America/Marigot (GMT-4:00)',
	'America/Martinique' => 'America/Martinique (GMT-4:00)',
	'America/Moncton' => 'America/Moncton (GMT-4:00)',
	'America/Montserrat' => 'America/Montserrat (GMT-4:00)',
	'America/Port_of_Spain' => 'America/Port_of_Spain (GMT-4:00)',
	'America/Porto_Acre' => 'America/Porto_Acre (GMT-4:00)',
	'America/Porto_Velho' => 'America/Porto_Velho (GMT-4:00)',
	'America/Puerto_Rico' => 'America/Puerto_Rico (GMT-4:00)',
	'America/Rio_Branco' => 'America/Rio_Branco (GMT-4:00)',
	'America/Santiago' => 'America/Santiago (GMT-4:00)',
	'America/Santo_Domingo' => 'America/Santo_Domingo (GMT-4:00)',
	'America/St_Barthelemy' => 'America/St_Barthelemy (GMT-4:00)',
	'America/St_Kitts' => 'America/St_Kitts (GMT-4:00)',
	'America/St_Lucia' => 'America/St_Lucia (GMT-4:00)',
	'America/St_Thomas' => 'America/St_Thomas (GMT-4:00)',
	'America/St_Vincent' => 'America/St_Vincent (GMT-4:00)',
	'America/Thule' => 'America/Thule (GMT-4:00)',
	'America/Tortola' => 'America/Tortola (GMT-4:00)',
	'America/Virgin' => 'America/Virgin (GMT-4:00)',
	'Antarctica/Palmer' => 'Antarctica/Palmer (GMT-4:00)',
	'Atlantic/Bermuda' => 'Atlantic/Bermuda (GMT-4:00)',
	'Atlantic/Stanley' => 'Atlantic/Stanley (GMT-4:00)',
	'Brazil/Acre' => 'Brazil/Acre (GMT-4:00)',
	'Brazil/West' => 'Brazil/West (GMT-4:00)',
	'Canada/Atlantic' => 'Canada/Atlantic (GMT-4:00)',
	'Chile/Continental' => 'Chile/Continental (GMT-4:00)',
	'America/St_Johns' => 'America/St_Johns (GMT-3:-30)',
	'Canada/Newfoundland' => 'Canada/Newfoundland (GMT-3:-30)',
	'America/Araguaina' => 'America/Araguaina (GMT-3:00)',
	'America/Bahia' => 'America/Bahia (GMT-3:00)',
	'America/Belem' => 'America/Belem (GMT-3:00)',
	'America/Buenos_Aires' => 'America/Buenos_Aires (GMT-3:00)',
	'America/Catamarca' => 'America/Catamarca (GMT-3:00)',
	'America/Cayenne' => 'America/Cayenne (GMT-3:00)',
	'America/Cordoba' => 'America/Cordoba (GMT-3:00)',
	'America/Fortaleza' => 'America/Fortaleza (GMT-3:00)',
	'America/Godthab' => 'America/Godthab (GMT-3:00)',
	'America/Jujuy' => 'America/Jujuy (GMT-3:00)',
	'America/Maceio' => 'America/Maceio (GMT-3:00)',
	'America/Mendoza' => 'America/Mendoza (GMT-3:00)',
	'America/Miquelon' => 'America/Miquelon (GMT-3:00)',
	'America/Montevideo' => 'America/Montevideo (GMT-3:00)',
	'America/Paramaribo' => 'America/Paramaribo (GMT-3:00)',
	'America/Recife' => 'America/Recife (GMT-3:00)',
	'America/Rosario' => 'America/Rosario (GMT-3:00)',
	'America/Santarem' => 'America/Santarem (GMT-3:00)',
	'America/Sao_Paulo' => 'America/Sao_Paulo (GMT-3:00)',
	'Antarctica/Rothera' => 'Antarctica/Rothera (GMT-3:00)',
	'Brazil/East' => 'Brazil/East (GMT-3:00)',
	'America/Noronha' => 'America/Noronha (GMT-2:00)',
	'Atlantic/South_Georgia' => 'Atlantic/South_Georgia (GMT-2:00)',
	'Brazil/DeNoronha' => 'Brazil/DeNoronha (GMT-2:00)',
	'America/Scoresbysund' => 'America/Scoresbysund (GMT-1:00)',
	'Atlantic/Azores' => 'Atlantic/Azores (GMT-1:00)',
	'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde (GMT-1:00)',
	'Africa/Abidjan' => 'Africa/Abidjan (GMT+0:00)',
	'Africa/Accra' => 'Africa/Accra (GMT+0:00)',
	'Africa/Bamako' => 'Africa/Bamako (GMT+0:00)',
	'Africa/Banjul' => 'Africa/Banjul (GMT+0:00)',
	'Africa/Bissau' => 'Africa/Bissau (GMT+0:00)',
	'Africa/Casablanca' => 'Africa/Casablanca (GMT+0:00)',
	'Africa/Conakry' => 'Africa/Conakry (GMT+0:00)',
	'Africa/Dakar' => 'Africa/Dakar (GMT+0:00)',
	'Africa/El_Aaiun' => 'Africa/El_Aaiun (GMT+0:00)',
	'Africa/Freetown' => 'Africa/Freetown (GMT+0:00)',
	'Africa/Lome' => 'Africa/Lome (GMT+0:00)',
	'Africa/Monrovia' => 'Africa/Monrovia (GMT+0:00)',
	'Africa/Nouakchott' => 'Africa/Nouakchott (GMT+0:00)',
	'Africa/Ouagadougou' => 'Africa/Ouagadougou (GMT+0:00)',
	'Africa/Sao_Tome' => 'Africa/Sao_Tome (GMT+0:00)',
	'Africa/Timbuktu' => 'Africa/Timbuktu (GMT+0:00)',
	'America/Danmarkshavn' => 'America/Danmarkshavn (GMT+0:00)',
	'Atlantic/Canary' => 'Atlantic/Canary (GMT+0:00)',
	'Atlantic/Faeroe' => 'Atlantic/Faeroe (GMT+0:00)',
	'Atlantic/Faroe' => 'Atlantic/Faroe (GMT+0:00)',
	'Atlantic/Madeira' => 'Atlantic/Madeira (GMT+0:00)',
	'Atlantic/Reykjavik' => 'Atlantic/Reykjavik (GMT+0:00)',
	'Atlantic/St_Helena' => 'Atlantic/St_Helena (GMT+0:00)',
	'Europe/Belfast' => 'Europe/Belfast (GMT+0:00)',
	'Europe/Dublin' => 'Europe/Dublin (GMT+0:00)',
	'Europe/Guernsey' => 'Europe/Guernsey (GMT+0:00)',
	'Europe/Isle_of_Man' => 'Europe/Isle_of_Man (GMT+0:00)',
	'Europe/Jersey' => 'Europe/Jersey (GMT+0:00)',
	'Europe/Lisbon' => 'Europe/Lisbon (GMT+0:00)',
	'Europe/London' => 'Europe/London (GMT+0:00)',
	'Africa/Algiers' => 'Africa/Algiers (GMT+1:00)',
	'Africa/Bangui' => 'Africa/Bangui (GMT+1:00)',
	'Africa/Brazzaville' => 'Africa/Brazzaville (GMT+1:00)',
	'Africa/Ceuta' => 'Africa/Ceuta (GMT+1:00)',
	'Africa/Douala' => 'Africa/Douala (GMT+1:00)',
	'Africa/Kinshasa' => 'Africa/Kinshasa (GMT+1:00)',
	'Africa/Lagos' => 'Africa/Lagos (GMT+1:00)',
	'Africa/Libreville' => 'Africa/Libreville (GMT+1:00)',
	'Africa/Luanda' => 'Africa/Luanda (GMT+1:00)',
	'Africa/Malabo' => 'Africa/Malabo (GMT+1:00)',
	'Africa/Ndjamena' => 'Africa/Ndjamena (GMT+1:00)',
	'Africa/Niamey' => 'Africa/Niamey (GMT+1:00)',
	'Africa/Porto-Novo' => 'Africa/Porto-Novo (GMT+1:00)',
	'Africa/Tunis' => 'Africa/Tunis (GMT+1:00)',
	'Africa/Windhoek' => 'Africa/Windhoek (GMT+1:00)',
	'Arctic/Longyearbyen' => 'Arctic/Longyearbyen (GMT+1:00)',
	'Atlantic/Jan_Mayen' => 'Atlantic/Jan_Mayen (GMT+1:00)',
	'Europe/Amsterdam' => 'Europe/Amsterdam (GMT+1:00)',
	'Europe/Andorra' => 'Europe/Andorra (GMT+1:00)',
	'Europe/Belgrade' => 'Europe/Belgrade (GMT+1:00)',
	'Europe/Berlin' => 'Europe/Berlin (GMT+1:00)',
	'Europe/Bratislava' => 'Europe/Bratislava (GMT+1:00)',
	'Europe/Brussels' => 'Europe/Brussels (GMT+1:00)',
	'Europe/Budapest' => 'Europe/Budapest (GMT+1:00)',
	'Europe/Copenhagen' => 'Europe/Copenhagen (GMT+1:00)',
	'Europe/Gibraltar' => 'Europe/Gibraltar (GMT+1:00)',
	'Europe/Ljubljana' => 'Europe/Ljubljana (GMT+1:00)',
	'Europe/Luxembourg' => 'Europe/Luxembourg (GMT+1:00)',
	'Europe/Madrid' => 'Europe/Madrid (GMT+1:00)',
	'Europe/Malta' => 'Europe/Malta (GMT+1:00)',
	'Europe/Monaco' => 'Europe/Monaco (GMT+1:00)',
	'Europe/Oslo' => 'Europe/Oslo (GMT+1:00)',
	'Europe/Paris' => 'Europe/Paris (GMT+1:00)',
	'Europe/Podgorica' => 'Europe/Podgorica (GMT+1:00)',
	'Europe/Prague' => 'Europe/Prague (GMT+1:00)',
	'Europe/Rome' => 'Europe/Rome (GMT+1:00)',
	'Europe/San_Marino' => 'Europe/San_Marino (GMT+1:00)',
	'Europe/Sarajevo' => 'Europe/Sarajevo (GMT+1:00)',
	'Europe/Skopje' => 'Europe/Skopje (GMT+1:00)',
	'Europe/Stockholm' => 'Europe/Stockholm (GMT+1:00)',
	'Europe/Tirane' => 'Europe/Tirane (GMT+1:00)',
	'Europe/Vaduz' => 'Europe/Vaduz (GMT+1:00)',
	'Europe/Vatican' => 'Europe/Vatican (GMT+1:00)',
	'Europe/Vienna' => 'Europe/Vienna (GMT+1:00)',
	'Europe/Warsaw' => 'Europe/Warsaw (GMT+1:00)',
	'Europe/Zagreb' => 'Europe/Zagreb (GMT+1:00)',
	'Europe/Zurich' => 'Europe/Zurich (GMT+1:00)',
	'Africa/Blantyre' => 'Africa/Blantyre (GMT+2:00)',
	'Africa/Bujumbura' => 'Africa/Bujumbura (GMT+2:00)',
	'Africa/Cairo' => 'Africa/Cairo (GMT+2:00)',
	'Africa/Gaborone' => 'Africa/Gaborone (GMT+2:00)',
	'Africa/Harare' => 'Africa/Harare (GMT+2:00)',
	'Africa/Johannesburg' => 'Africa/Johannesburg (GMT+2:00)',
	'Africa/Kigali' => 'Africa/Kigali (GMT+2:00)',
	'Africa/Lubumbashi' => 'Africa/Lubumbashi (GMT+2:00)',
	'Africa/Lusaka' => 'Africa/Lusaka (GMT+2:00)',
	'Africa/Maputo' => 'Africa/Maputo (GMT+2:00)',
	'Africa/Maseru' => 'Africa/Maseru (GMT+2:00)',
	'Africa/Mbabane' => 'Africa/Mbabane (GMT+2:00)',
	'Africa/Tripoli' => 'Africa/Tripoli (GMT+2:00)',
	'Asia/Amman' => 'Asia/Amman (GMT+2:00)',
	'Asia/Beirut' => 'Asia/Beirut (GMT+2:00)',
	'Asia/Damascus' => 'Asia/Damascus (GMT+2:00)',
	'Asia/Gaza' => 'Asia/Gaza (GMT+2:00)',
	'Asia/Istanbul' => 'Asia/Istanbul (GMT+2:00)',
	'Asia/Jerusalem' => 'Asia/Jerusalem (GMT+2:00)',
	'Asia/Nicosia' => 'Asia/Nicosia (GMT+2:00)',
	'Asia/Tel_Aviv' => 'Asia/Tel_Aviv (GMT+2:00)',
	'Europe/Athens' => 'Europe/Athens (GMT+2:00)',
	'Europe/Bucharest' => 'Europe/Bucharest (GMT+2:00)',
	'Europe/Chisinau' => 'Europe/Chisinau (GMT+2:00)',
	'Europe/Helsinki' => 'Europe/Helsinki (GMT+2:00)',
	'Europe/Istanbul' => 'Europe/Istanbul (GMT+2:00)',
	'Europe/Kaliningrad' => 'Europe/Kaliningrad (GMT+2:00)',
	'Europe/Kiev' => 'Europe/Kiev (GMT+2:00)',
	'Europe/Mariehamn' => 'Europe/Mariehamn (GMT+2:00)',
	'Europe/Minsk' => 'Europe/Minsk (GMT+2:00)',
	'Europe/Nicosia' => 'Europe/Nicosia (GMT+2:00)',
	'Europe/Riga' => 'Europe/Riga (GMT+2:00)',
	'Europe/Simferopol' => 'Europe/Simferopol (GMT+2:00)',
	'Europe/Sofia' => 'Europe/Sofia (GMT+2:00)',
	'Europe/Tallinn' => 'Europe/Tallinn (GMT+2:00)',
	'Europe/Tiraspol' => 'Europe/Tiraspol (GMT+2:00)',
	'Europe/Uzhgorod' => 'Europe/Uzhgorod (GMT+2:00)',
	'Europe/Vilnius' => 'Europe/Vilnius (GMT+2:00)',
	'Europe/Zaporozhye' => 'Europe/Zaporozhye (GMT+2:00)',
	'Africa/Addis_Ababa' => 'Africa/Addis_Ababa (GMT+3:00)',
	'Africa/Asmara' => 'Africa/Asmara (GMT+3:00)',
	'Africa/Asmera' => 'Africa/Asmera (GMT+3:00)',
	'Africa/Dar_es_Salaam' => 'Africa/Dar_es_Salaam (GMT+3:00)',
	'Africa/Djibouti' => 'Africa/Djibouti (GMT+3:00)',
	'Africa/Kampala' => 'Africa/Kampala (GMT+3:00)',
	'Africa/Khartoum' => 'Africa/Khartoum (GMT+3:00)',
	'Africa/Mogadishu' => 'Africa/Mogadishu (GMT+3:00)',
	'Africa/Nairobi' => 'Africa/Nairobi (GMT+3:00)',
	'Antarctica/Syowa' => 'Antarctica/Syowa (GMT+3:00)',
	'Asia/Aden' => 'Asia/Aden (GMT+3:00)',
	'Asia/Baghdad' => 'Asia/Baghdad (GMT+3:00)',
	'Asia/Bahrain' => 'Asia/Bahrain (GMT+3:00)',
	'Asia/Kuwait' => 'Asia/Kuwait (GMT+3:00)',
	'Asia/Qatar' => 'Asia/Qatar (GMT+3:00)',
	'Europe/Moscow' => 'Europe/Moscow (GMT+3:00)',
	'Europe/Volgograd' => 'Europe/Volgograd (GMT+3:00)',
	'Indian/Antananarivo' => 'Indian/Antananarivo (GMT+3:00)',
	'Indian/Comoro' => 'Indian/Comoro (GMT+3:00)',
	'Indian/Mayotte' => 'Indian/Mayotte (GMT+3:00)',
	'Asia/Tehran' => 'Asia/Tehran (GMT+3:30)',
	'Asia/Baku' => 'Asia/Baku (GMT+4:00)',
	'Asia/Dubai' => 'Asia/Dubai (GMT+4:00)',
	'Asia/Muscat' => 'Asia/Muscat (GMT+4:00)',
	'Asia/Tbilisi' => 'Asia/Tbilisi (GMT+4:00)',
	'Asia/Yerevan' => 'Asia/Yerevan (GMT+4:00)',
	'Europe/Samara' => 'Europe/Samara (GMT+4:00)',
	'Indian/Mahe' => 'Indian/Mahe (GMT+4:00)',
	'Indian/Mauritius' => 'Indian/Mauritius (GMT+4:00)',
	'Indian/Reunion' => 'Indian/Reunion (GMT+4:00)',
	'Asia/Kabul' => 'Asia/Kabul (GMT+4:30)',
	'Asia/Aqtau' => 'Asia/Aqtau (GMT+5:00)',
	'Asia/Aqtobe' => 'Asia/Aqtobe (GMT+5:00)',
	'Asia/Ashgabat' => 'Asia/Ashgabat (GMT+5:00)',
	'Asia/Ashkhabad' => 'Asia/Ashkhabad (GMT+5:00)',
	'Asia/Dushanbe' => 'Asia/Dushanbe (GMT+5:00)',
	'Asia/Karachi' => 'Asia/Karachi (GMT+5:00)',
	'Asia/Oral' => 'Asia/Oral (GMT+5:00)',
	'Asia/Samarkand' => 'Asia/Samarkand (GMT+5:00)',
	'Asia/Tashkent' => 'Asia/Tashkent (GMT+5:00)',
	'Asia/Yekaterinburg' => 'Asia/Yekaterinburg (GMT+5:00)',
	'Indian/Kerguelen' => 'Indian/Kerguelen (GMT+5:00)',
	'Indian/Maldives' => 'Indian/Maldives (GMT+5:00)',
	'Asia/Calcutta' => 'Asia/Calcutta (GMT+5:30)',
	'Asia/Colombo' => 'Asia/Colombo (GMT+5:30)',
	'Asia/Kolkata' => 'Asia/Kolkata (GMT+5:30)',
	'Asia/Katmandu' => 'Asia/Katmandu (GMT+5:45)',
	'Antarctica/Mawson' => 'Antarctica/Mawson (GMT+6:00)',
	'Antarctica/Vostok' => 'Antarctica/Vostok (GMT+6:00)',
	'Asia/Almaty' => 'Asia/Almaty (GMT+6:00)',
	'Asia/Bishkek' => 'Asia/Bishkek (GMT+6:00)',
	'Asia/Dacca' => 'Asia/Dacca (GMT+6:00)',
	'Asia/Dhaka' => 'Asia/Dhaka (GMT+6:00)',
	'Asia/Novosibirsk' => 'Asia/Novosibirsk (GMT+6:00)',
	'Asia/Omsk' => 'Asia/Omsk (GMT+6:00)',
	'Asia/Qyzylorda' => 'Asia/Qyzylorda (GMT+6:00)',
	'Asia/Thimbu' => 'Asia/Thimbu (GMT+6:00)',
	'Asia/Thimphu' => 'Asia/Thimphu (GMT+6:00)',
	'Indian/Chagos' => 'Indian/Chagos (GMT+6:00)',
	'Asia/Rangoon' => 'Asia/Rangoon (GMT+6:30)',
	'Indian/Cocos' => 'Indian/Cocos (GMT+6:30)',
	'Antarctica/Davis' => 'Antarctica/Davis (GMT+7:00)',
	'Asia/Bangkok' => 'Asia/Bangkok (GMT+7:00)',
	'Asia/Ho_Chi_Minh' => 'Asia/Ho_Chi_Minh (GMT+7:00)',
	'Asia/Hovd' => 'Asia/Hovd (GMT+7:00)',
	'Asia/Jakarta' => 'Asia/Jakarta (GMT+7:00)',
	'Asia/Krasnoyarsk' => 'Asia/Krasnoyarsk (GMT+7:00)',
	'Asia/Phnom_Penh' => 'Asia/Phnom_Penh (GMT+7:00)',
	'Asia/Pontianak' => 'Asia/Pontianak (GMT+7:00)',
	'Asia/Saigon' => 'Asia/Saigon (GMT+7:00)',
	'Asia/Vientiane' => 'Asia/Vientiane (GMT+7:00)',
	'Indian/Christmas' => 'Indian/Christmas (GMT+7:00)',
	'Antarctica/Casey' => 'Antarctica/Casey) (GMT+8:00)',
	'Asia/Brunei' => 'Asia/Brunei (GMT+8:00)',
	'Asia/Choibalsan' => 'Asia/Choibalsan (GMT+8:00)',
	'Asia/Chongqing' => 'Asia/Chongqing (GMT+8:00)',
	'Asia/Chungking' => 'Asia/Chungking (GMT+8:00)',
	'Asia/Harbin' => 'Asia/Harbin (GMT+8:00)',
	'Asia/Hong_Kong' => 'Asia/Hong_Kong (GMT+8:00)',
	'Asia/Irkutsk' => 'Asia/Irkutsk (GMT+8:00)',
	'Asia/Kashgar' => 'Asia/Kashgar (GMT+8:00)',
	'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur (GMT+8:00)',
	'Asia/Kuching' => 'Asia/Kuching (GMT+8:00)',
	'Asia/Macao' => 'Asia/Macao (GMT+8:00)',
	'Asia/Macau' => 'Asia/Macau (GMT+8:00)',
	'Asia/Makassar' => 'Asia/Makassar (GMT+8:00)',
	'Asia/Manila' => 'Asia/Manila (GMT+8:00)',
	'Asia/Shanghai' => 'Asia/Shanghai (GMT+8:00)',
	'Asia/Singapore' => 'Asia/Singapore (GMT+8:00)',
	'Asia/Taipei' => 'Asia/Taipei (GMT+8:00)',
	'Asia/Ujung_Pandang' => 'Asia/Ujung_Pandang (GMT+8:00)',
	'Asia/Ulaanbaatar' => 'Asia/Ulaanbaatar (GMT+8:00)',
	'Asia/Ulan_Bator' => 'Asia/Ulan_Bator (GMT+8:00)',
	'Asia/Urumqi' => 'Asia/Urumqi (GMT+8:00)',
	'Australia/Perth' => 'Australia/Perth) (GMT+8:00)',
	'Australia/West' => 'Australia/West) (GMT+8:00)',
	'Australia/Eucla' => 'Australia/Eucla) (GMT+8:45)',
	'Asia/Dili' => 'Asia/Dili (GMT+9:00)',
	'Asia/Jayapura' => 'Asia/Jayapura (GMT+9:00)',
	'Asia/Pyongyang' => 'Asia/Pyongyang (GMT+9:00)',
	'Asia/Seoul' => 'Asia/Seoul (GMT+9:00)',
	'Asia/Tokyo' => 'Asia/Tokyo (GMT+9:00)',
	'Asia/Yakutsk' => 'Asia/Yakutsk (GMT+9:00)',
	'Australia/Adelaide' => 'Australia/Adelaide) (GMT+9:30)',
	'Australia/Broken_Hill' => 'Australia/Broken_Hill) (GMT+9:30)',
	'Australia/Darwin' => 'Australia/Darwin) (GMT+9:30)',
	'Australia/North' => 'Australia/North) (GMT+9:30)',
	'Australia/South' => 'Australia/South) (GMT+9:30)',
	'Australia/Yancowinna' => 'Australia/Yancowinna) (GMT+9:30)',
	'Antarctica/DumontDUrville' => 'Antarctica/DumontDUrville (GMT+10:00)',
	'Asia/Sakhalin' => 'Asia/Sakhalin (GMT+10:00)',
	'Asia/Vladivostok' => 'Asia/Vladivostok (GMT+10:00)',
	'Australia/ACT' => 'Australia/ACT) (GMT+10:00)',
	'Australia/Brisbane' => 'Australia/Brisbane) (GMT+10:00)',
	'Australia/Canberra' => 'Australia/Canberra) (GMT+10:00)',
	'Australia/Currie' => 'Australia/Currie) (GMT+10:00)',
	'Australia/Hobart' => 'Australia/Hobart) (GMT+10:00)',
	'Australia/Lindeman' => 'Australia/Lindeman) (GMT+10:00)',
	'Australia/Melbourne' => 'Australia/Melbourne) (GMT+10:00)',
	'Australia/NSW' => 'Australia/NSW) (GMT+10:00)',
	'Australia/Queensland' => 'Australia/Queensland) (GMT+10:00)',
	'Australia/Sydney' => 'Australia/Sydney) (GMT+10:00)',
	'Australia/Tasmania' => 'Australia/Tasmania) (GMT+10:00)',
	'Australia/Victoria' => 'Australia/Victoria) (GMT+10:00)',
	'Australia/LHI' => 'Australia/LHI (GMT+10:30)',
	'Australia/Lord_Howe' => 'Australia/Lord_Howe (GMT+10:30)',
	'Asia/Magadan' => 'Asia/Magadan (GMT+11:00)',
	'Antarctica/McMurdo' => 'Antarctica/McMurdo (GMT+12:00)',
	'Antarctica/South_Pole' => 'Antarctica/South_Pole (GMT+12:00)',
	'Asia/Anadyr' => 'Asia/Anadyr (GMT+12:00)',
	'Asia/Kamchatka' => 'Asia/Kamchatka (GMT+12:00)'
);
sort($timezones);
$mainPath = dirname(__DIR__);
$mainPath = str_replace('install/home', '', $mainPath);
$paths = preg_match('/\/public_html/', $mainPath) ? explode('/public_html/', $mainPath) : array('/', '');
$path = $paths[1];
$sqlPathMain = __DIR__ . '/sqls/db__structure.sql';
$sqlPathSettings = __DIR__ . '/sqls/db__settings_data.sql';
$steps = array(1, 2, 3, 4, 5, 6, 7);
$_GET['step'] = isset($_GET['step']) && ctype_digit($_GET['step']) && in_array($_GET['step'], $steps) ? $_GET['step'] : 1;
?><div class="header">
	<h1>MTG Codes v9.1</h1>
	<h2>Installation</h2>
	<h3>Progress: <div class="pure-u-<?php echo $_GET['step']; ?>-<?php echo count($steps); ?>"></div></h3>
	<p>Step <?php echo $_GET['step']; ?> of <?php echo count($steps); ?></p>
</div>
<div class="content"><?php
switch($_GET['step']) {
	default: case 1:
		?><h2 class="content-subhead">Let's do some checks first...</h2>
		<p>
			<form action='install.php?step=2' method='post' class='pure-form'>
				<table class='pure-table' width='75%'>
					<tr>
						<th width='25%'>PHP Version</th>
						<td width='75%'><span style='color:<?php echo PHP_VERSION_ID >= 50400 ? 'green' : 'red'; ?>'><?php echo PHP_VERSION; ?></span></td>
					</tr>
					<tr>
						<th>Main SQL File</th>
						<td><?php echo is_file($sqlPathMain) ? "<span style='color:green;'>Exists</span>" : "<span style='color:red;'>Doesn't exist!</span>"; ?></td>
					</tr>
					<tr>
						<th>Settings SQL File</th>
						<td><?php echo is_file($sqlPathSettings) ? "<span style='color:green;'>Exists</span>" : "<span style='color:red;'>Doesn't exist!</span>"; ?></td>
					</tr>
					<tr>
						<th>Game Directory</th>
						<td><input type='text' name='gamedir' value='/<?php echo $path; ?>' size='100%' /></td>
					</tr>
					<tr>
						<td colspan='2' class='center'><input type='submit' value='Check' class='pure-button pure-button-primary' /></td>
					</tr>
				</table>
			</form>
			*<strong>Game Directory:</strong> This is simply where you've uploaded the game - this is normally <code>/</code>.<br />
			Make sure that <code>/includes/config.php</code> is writable.<br />
			If you're not sure, just leave it blank and I'll try to install it anyway!
		</p><?php
		break;
	case 2:
		$_POST['gamedir'] = isset($_POST['gamedir']) && is_string($_POST['gamedir']) ? $_POST['gamedir'] : null;
		$path = !empty($_POST['gamedir']) ? $_POST['gamedir'] : '/';
		$path = str_replace('//', '/', $mainPath . $path);
		if(!is_dir($path))
			error("That's not a valid directory path: ".$path);
		if(!is_dir($path))
			error("I couldn't find that directory. Are you sure you've entered the correct game path?");
		?><h2 class='content-subhead'>That checks out fine!</h2>
		<p>
			<form action='install.php?step=3' method='post' class='pure-form'>
				<input type='hidden' name='gamedir' value='<?php echo $path; ?>' />
				<table class='pure-table' width='75%'>
					<tr>
						<th colspan='2' class='center'>Database Configuration</th>
					</tr>
					<tr>
						<th width='25%'>Host</th>
						<td width='75%'><input type='text' name='host' value='localhost' size='100%' /></td>
					</tr>
					<tr>
						<th>User</th>
						<td><input type='text' name='user' placeholder='root' size='100%' /></td>
					</tr>
					<tr>
						<th>Password</th>
						<td><input type='password' name='pass' size='100%' /></td>
					</tr>
					<tr>
						<th>Database</th>
						<td><input type='text' name='name' size='100%' /></td>
					</tr>
					<tr>
						<th>Time Offset</th>
						<td><select name='timezone'><?php
						foreach($timezones as $val => $dis)
							printf("<option value='%s'>%s</option>", $val, str_replace(array('_', '/'), array(' ', ': '), $dis));
						?></select></td>
					</tr>
					<tr>
						<td colspan='2' class='center'><input type='submit' value='Connect' class='pure-button pure-button-primary' /></td>
					</tr>
				</table>
			</form>
			*<strong>Host:</strong> This mostly speaks for itself. You need to enter the URL to your MySQL database.<br />
			&nbsp;&nbsp;&nbsp;&nbsp;- For most people, it's normally <code>localhost</code>, which is filled in by default.<br />
			*<strong>User:</strong> The name of the user you created when creating the database.<br />
			*<strong>Pass:</strong> This is the password you entered when creating the user.<br />
			*<strong>Database:</strong> And finally, the name of the database itself!
		</p><?php
		break;
	case 3:
		$_POST['gamedir'] = isset($_POST['gamedir']) && is_string($_POST['gamedir']) ? $_POST['gamedir'] : null;
		$path = !empty($_POST['gamedir']) ? $_POST['gamedir'] : '';
		if(!is_dir($path))
			error("That's not a valid directory path");
		$includeDir = rtrim($path, '/').'/includes';
		$configFile = $includeDir.'/config.php';
		if(!is_dir($path))
			error("I couldn't find that directory. Are you sure you've entered the correct game path?");
		$configuration = "<?php
if(!defined('MTG_ENABLE'))
	exit;
date_default_timezone_set('".$_POST['timezone']."');
define('DB_HOST', '".$_POST['host']."');
define('DB_USER', '".$_POST['user']."');
define('DB_PASS', '".$_POST['pass']."');
define('DB_NAME', '".$_POST['name']."');";
		if(!file_exists($configFile)) {
			info("The configuration file (<code>".$configFile."</code>) couldn't be found. Trying to create it now...");
			$creation = @fopen($configFile, 'w');
			if(!$creation)
				error("I couldn't open the config.php to edit! Please manually create it in the <code>/includes</code> directory");
			fwrite($creation, $configuration);
			fclose($creation);
			if(!$creation || !file_exists($configFile))
				error("The configuration file couldn't be created");
			else
				success("The configuration file has been created");
		}
		if(file_exists($configFile) && !is_writeable($configFile)) {
			?>Code required:<br /><textarea class='pure-input-1-2' rows='10' cols='70'><?php echo $configuration; ?></textarea><br /><?php
			error("Unfortunately, the config.php exists, but couldn't be modified. Please make sure your <code>/includes/</code> directory and <code>/includes/config.php</code> is writeable - or edit the file manually");
		} else {
			$creation = fopen($configFile, 'w');
			if(!$creation)
				error("I couldn't edit the config.php");
			fwrite($creation, $configuration);
			fclose($creation);
			if(!$creation || !file_exists($configFile))
				error("The configuration file couldn't be created");
			else
				success("The configuration file has been created");
		}
		info("Attempting connection to the database..");
		require_once($mainPath . '/includes/class/class_mtg_db_mysqli.php');
		success("I do believe we've connected! Moving on...<meta http-equiv='refresh' content='2; url=install.php?step=4' />");
		break;
	case 4:
		require_once($mainPath . '/includes/class/class_mtg_db_mysqli.php');
		?><h2 class='content-subhead'>We're connected! Let's install the database</h2><?php
		$templineMain = '';
		$lines = file($sqlPathMain);
		foreach ($lines as $line) {
			if(substr($line, 0, 2) == '--' || !$line)
				continue;
			$templineMain .= $line;
			if (substr(trim($line), -1, 1) == ';') {
				$db->query($templineMain);
				$db->execute();
				$templineMain = '';
			}
		}
		$templineSettings = '';
		$lines = file($sqlPathSettings);
		foreach ($lines as $line) {
			if(substr($line, 0, 2) == '--' || !$line)
				continue;
			$templineSettings .= $line;
			if (substr(trim($line), -1, 1) == ';') {
				$db->query($templineSettings);
				$db->execute();
				$templineSettings = '';
			}
		}
		if($db->tableExists('users'))
			success("Database installed, let's move on.<meta http-equiv='refresh' content='2; url=install.php?step=5' />");
		else
			error("The database didn't install.. Try importing it manually");
		break;
	case 5:
		?><h2 class='content-subhead'>Database installed, let's configure the game</h2>
		<form action='install.php?step=6' method='post' class='pure-form-aligned'>
			<table class='pure-table' width='75%'>
				<tr>
					<th colspan='2' class='center'>Basic Settings</th>
				</tr>
				<tr>
					<th width='25%'>Game Name</th>
					<td width='75%'><input type='text' name='game_name' /></td>
				</tr>
				<tr>
					<th>Game Owner</th>
					<td><input type='text' name='game_owner' /></td>
				</tr>
				<tr>
					<th>Game Description</th>
					<td><textarea style='width:75%;' rows='10' name='game_description'></textarea></td>
				</tr>
				<tr>
					<th colspan='2' class='center'>Your Account</th>
				</tr>
				<tr>
					<th>Username</th>
					<td><input type='text' name='username' /></td>
				</tr>
				<tr>
					<th>Password</th>
					<td><input type='password' name='pass' /></td>
				</tr>
				<tr>
					<th>Confirm Password</th>
					<td><input type='password' name='cpass' /></td>
				</tr>
				<tr>
					<th>Email</th>
					<td><input type='email' name='email' /></td>
				</tr>
				<tr>
					<td colspan='2' class='center'><input type='submit' class='pure-button pure-button-primary' name='submit' value='Modify Settings and Create Account' /></td>
				</tr>
			</table>
		</form>
		<p>
			*<strong>Game Name:</strong> The name of your game<br />
			*<strong>Game Owner:</strong> Put your name on it, it's yours!<br />
			*<strong>Game Description:</strong> Add a description of your game. Think quality over quantity ;)<br />
		</p><?php
		break;
	case 6:
		if(!isset($_POST['submit']))
			error("You didn't come from step 5..");
		if(empty($_POST['game_name']))
			error("You must enter a game name. If you're not sure, enter a temporarily value (such as &ldquo;To Be Named&rdquo;, for example)");
		require_once($mainPath . '/includes/class/class_mtg_db_mysqli.php');
		$db->query("UPDATE `game_settings` SET `value` = :value WHERE `name` = :name");
		$settings = array(
			array(
				':value' => $_POST['game_name'],
				':name' => 'game_name'
			),
			array(
				':value' => $_POST['game_owner'],
				':name' => 'game_owner'
			),
			array(
				':value' => $_POST['game_description'],
				':name' => 'game_description'
			)
		);
		$settings = array_shift($settings);
		foreach($settings as $param => $value)
			$db->bind($param, $value);
		$db->execute();
		if(empty($_POST['username']))
			error("You didn't enter a valid username");
		if(empty($_POST['pass']))
			error("You didn't enter a valid password");
		if(empty($_POST['cpass']))
			error("You didn't enter a valid password confirmation");
		if($_POST['pass'] != $_POST['cpass'])
			error("Your passwords didn't match");
		require_once($mainPath . '/includes/class/class_mtg_users.php');
		$pass = $users->hashPass($_POST['pass']);
		$db->query("INSERT INTO `staff_ranks` (`rank_id`, `rank_name`, `rank_order`, `rank_colour`, `override_all`) VALUES (1, 'Owner', 1, '000033', 'Yes');");
		$db->execute();
		$db->query("INSERT INTO `users` (`id`, `username`, `password`, `email`, `staff_rank`) VALUES (?, ?, ?, ?, ?)");
		$db->execute(array(1, $_POST['username'], $pass, $_POST['email'], 1));
		success("Your game's basic settings have been installed and your account has been created!");
		?>I recommend that you remove this installation directory (keep a local backup, just in case).<br />
		I can try to delete it for you now if you'd like?<br />
		<a href='install.php?step=7'>Yes, try and remove this directory</a><?php
		break;
	case 7:
		delete_files(__DIR__);
		if(!is_file(__DIR__ . '/index.php'))
			success("I've managed to delete this install folder. Have fun!");
		else {
			$extra = null;
			if(chmod(__DIR__, 0600))
				$extra = "<br />I've managed to set the directory permissions to 0600. That should offer a little protection, but I still highly recommend you delete this folder!";
			warning("I couldn't delete this folder. Please manually delete it.".$extra);
		}
		break;
}
function delete_files($dir) {
	if(is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if($object != '.' && $object != '..') {
				if(filetype($dir.'/'.$object) == "dir")
					delete_files($dir.'/'.$object);
				else
					unlink($dir.'/'.$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}
?></div>