<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
	
function absNumber($number){return rtrim((strpos($number,".") !== false ? rtrim($number, "0") : $number),".");}

function nestedTextCase($value,$case="U") {
	if (is_array($value)) {return array_map('nestedTextCase', $value);}
	if($case=="L"){return strtolower($value);}else{return strtoupper($value);}
}

function numberOfDecimals($value){
	if ((int)$value == $value)
	{
		return 0;
	}
	else if (! is_numeric($value))
	{
		// throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
		return false;
	}

	return strlen($value) - strrpos($value, '.') - 1;
}

function numToWordCurrency($number,$formatType="SPELLOUT",$currencyType=""){
    if(empty($currencyType)){$currencyType=locale_get_default();}
    if($formatType == "SPELLOUT"):
        $currencyFormat = new \NumberFormatter( $currencyType, \NumberFormatter::SPELLOUT );
    else:
        //$currencyFormat = new \NumberFormatter( $currencyType, \NumberFormatter::CURRENCY );
        $currencyFormat = new NumberFormatter($locale = 'en_IN', NumberFormatter::SPELLOUT);
    endif;
    return  ucwords($currencyFormat->format($number));
}

function formatDecimal($num,$format=''){
    return number_format($num,2);
    if(empty($format))
    {
        $nums = explode(".",$num); // 10000
        if(count($nums)>2){return "0";}
        else
        {
            if(count($nums)==1){$nums[1]="00";}
            $num = $nums[0];$explrestunits = "" ;
            if(strlen($num)>3){
                $lastthree = substr($num, strlen($num)-3, strlen($num)); // 000
                $restunits = substr($num, 0, strlen($num)-3); // 10
                $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
                $expunit = str_split($restunits, 2);
                for($i=0; $i<sizeof($expunit); $i++){if($i==0){$explrestunits .= (int)$expunit[$i].","; }else{$explrestunits .= $expunit[$i].",";}}
                $thecash = $explrestunits.$lastthree;
            }
            else {$thecash = $num;}
            return $thecash.".".$nums[1]; 
        }
    }
    else{return number_format((floatVal($num)) ,2);}
}

function numToWordEnglish($number){
	$number=number_format($number, 2, '.', '');
	$no=abs($number);
	$no1=$number;
	$hundred = null;
	$digits_1 = strlen($no);
	$i = 0;
	$str = array();
	$words = array('0' => '', '1' => 'One', '2' => 'Two',
	'3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
	'7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
	'10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
	'13' => 'Thirteen', '14' => 'Fourteen',
	'15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
	'18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
	'30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
	'60' => 'Sixty', '70' => 'Seventy',
	'80' => 'Eighty', '90' => 'Ninety');
	
	$dictionary  = array(
		0                   => 'Zero',
		1                   => 'One',
		2                   => 'Two',
		3                   => 'Three',
		4                   => 'Four',
		5                   => 'Five',
		6                   => 'Six',
		7                   => 'Seven',
		8                   => 'Eight',
		9                   => 'Nine',
		10                  => 'Ten',
		11                  => 'Eleven',
		12                  => 'Twelve',
		13                  => 'Thirteen',
		14                  => 'Fourteen',
		15                  => 'Fifteen',
		16                  => 'Sixteen',
		17                  => 'Seventeen',
		18                  => 'Eighteen',
		19                  => 'Nineteen',
		20                  => 'Twenty',
		30                  => 'Thirty',
		40                  => 'Fourty',
		50                  => 'Fifty',
		60                  => 'Sixty',
		70                  => 'Seventy',
		80                  => 'Eighty',
		90                  => 'Ninety',
		100                 => 'Hundred',
		1000                => 'Thousand',
		1000000             => 'Million',
		1000000000          => 'Billion',
		1000000000000       => 'Trillion',
		1000000000000000    => 'Quadrillion',
		1000000000000000000 => 'Quintillion'
	);
	
	$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
	while ($i < $digits_1) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += ($divider == 10) ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str [] = ($number < 21) ? $words[$number] .
				" " . $digits[$counter] . $plural . " " . $hundred
				:
				$words[floor($number / 10) * 10]
				. " " . $words[$number % 10] . " "
				. $digits[$counter] . $plural . " " . $hundred;
		} else $str[] = null;
	}
	$str = array_reverse($str);
	$result = implode('', $str);
  
	$decimal     = ' point ';
	$string1 = $fraction = null;
	
	$hundred = null;
	$str1=array();
	$i = 0;
	$point = "0";
	if (strpos($no1, '.') !== false) {$arno = explode('.',$no1);$point = $arno['1'];}else{$point = "0";}
	
	$fraction=$point;
	if($fraction>0)
	{
		$pl = strlen($point);
		if (null !== $fraction && is_numeric($fraction)) {
			$string1 .= $decimal;
			if($fraction<10){
				$string1 .= " Zero ";
			}
			
			$digits_2=$fraction;
			while ($i < $digits_2) {
				$divider = ($i == 2) ? 10 : 100;
				$number = floor($fraction % $divider);
				$fraction = floor($fraction / $divider);
				$i += ($divider == 10) ? 1 : 2;
				if ($number) {
					$plural = (($counter = count($str1)) && $number > 9) ? 's' : null;
					$hundred = ($counter == 1 && $str1[0]) ? ' and ' : null;
					$str1 [] = ($number < 21) ? $words[$number] .
						" " . $digits[$counter] . $plural . " " . $hundred
						:
						$words[floor($number / 10) * 10]
						. " " . $words[$number % 10] . " "
						. $digits[$counter] . $plural . " " . $hundred;
				} else $str1[] = null;
			}
			$str1 = array_reverse($str1);
			$string1 .= implode('', $str1);
		}
	}
	return $result.$string1." Only"; // writes the final format where $currency is the currency symbol.
}

function numToWordGujarati($number){
	$number=abs($number);
	if (strpos($number, '.') !== false) 
	{
		$arno = explode('.',$number);
		$no = $arno['0'];
		$point = $arno['1'];
	}
	else
	{
		$no = $number;
		$point = "0";
	}
	
	$pl = strlen($point);
	if($pl==1){$point=$point*10;}else{$point=(int)$point;}
	
	$hundred = null;
	$digits_1 = strlen($no);
	$i = 0;
	$str = array();
	$words = array(
			0=> 'શૂન્ય',1=> 'એક',2=> 'બે',3=> 'ત્રણ',4=> 'ચાર',5=> 'પાંચ',6=> 'છ',7=> 'સાત',8=> 'આઠ',9=> 'નવ',10=> 'દસ',11=> 'અગિયાર',12=> 'બાર',13=> 'તેર',
			14=> 'ચવુદ',15=> 'પંદર',16=> 'સોળ',17=> 'સતર',18=> 'અઢાર',19=> 'ઓગણીસ',20=>'વીસ',21=> "એકવીસ",	22=> "બાવીસ",23=> "તેવીસ",24=>  "ચોવીસ",
			25=> "પચ્ચીસ",26 => "છવીસ",27=> "સત્તાવીસ",28=>  "અઠ્ઠાવીસ",29=> "ઓગણત્રીસ",30=>"ત્રીસ",31=> "એકત્રીસ",32=>  "બત્રીસ",33=>  "તેત્રીસ",34 => "ચોત્રીસ",
			35=> "પાંત્રીસ",36=> "છત્રીસ",37=> "સડત્રીસ",38=>  "અડત્રીસ",39 => "ઓગણચાલીસ",40=>"ચાલીસ",41=>  "એકતાલીસ",42=>  "બેતાલીસ",43=> "ત્રેતાલીસ",
			44=> "ચુંમાલીસ",45=>  "પિસ્તાલીસ",46=> "છેતાલીસ",47=>  "સુડતાલીસ",48=> "અડતાલીસ",49=>  "ઓગણપચાસ",50=> "પચાસ",51=> "એકાવન",52=> "બાવન",
			53=>  "ત્રેપન",54=>  "ચોપન",55=>  "પંચાવન",56=> "છપ્પન",57=> "સત્તાવન",58=> "અઠ્ઠાવન",59=>  "ઓગણસાઠ",60=>  "સાઈઠ",61=>  "એકસઠ",62=> "બાસઠ",
			63=> "ત્રેસઠ",64=> "ચોસઠ",65=>  "પાંસઠ",66=>  "છાસઠ",67=>  "સડસઠ",68=>  "સડસઠ",69=>  "અગણોસિત્તેર",70=> "સિત્તેર",71=>  "એકોતેર",72=> "બોતેર ",
			73=> "તોતેર",74=> "ચુમોતેર",75=> "પંચોતેર",76=> "છોતેર",77=> "સિત્યોતેર",78 => "ઇઠ્યોતેર",79=>  "ઓગણાએંસી",80=>  "એંસી",81=>  "એક્યાસી",82=>"બ્યાસી",
			83=>  "ત્યાસી",84=>  "ચોર્યાસી",85=>  "પંચાસી",86=> "છ્યાસી",87=> "સિત્યાસી",88=> "ઈઠ્યાસી",89=>  "નેવ્યાસી",90=>  "નેવું",91=> "એકાણું",92=> "બાણું",
			93=>  "ત્રાણું",94=> "ચોરાણું",95=> "પંચાણું",96=> "છન્નું",97=> "સત્તાણું",98=> "અઠ્ઠાણું",99=> "નવ્વાણું"
	);
	
	$digits = array('', 'સો', 'હજાર', 'લાખ', 'કરોડ','અબજ','ખરવ');
	while ($i < $digits_1) 
	{
		$divider = ($i == 2) ? 10 : 100;
		$number = ($no % $divider);
		$no = ($no / $divider);
		$i += ($divider == 10) ? 1 : 2;
		if ($number) 
		{
			$plural = (($counter = count($str)) && $number > 9) ? '' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' ' : null;
			$str [] = ($number < 100) ? $words[$number] .
				" " . $digits[$counter] . $plural . " " . $hundred
				:
				$words[($number / 10) * 10]
				. " " . $words[$number % 10] . " "
				. $digits[$counter] . $plural . " " . $hundred;
		} else $str[] = null;
	}
	$str = array_reverse($str);
	$result = implode('', $str);
	
	$rval="";
	if($result==""){$result= "શૂન્ય";}
	if($result=="શૂન્ય" and $words[$point]=="શૂન્ય"){$rval="શૂન્ય રૂપિયા";}
	elseif($words[$point]=="શૂન્ય"){$rval=$result . " રૂપિયા પુરા";}else{$rval=$result . " રૂપિયા" . " અને " .$words[$point] . " પૈસા પુરા";}
	//else{$rval=$result . " રૂપિયા" . " અને " .$words[$point] . " પૈસા પુરા";}
	
	return $rval;
	//return str_replace("બે સો","બસો",$rval);
}

function E2G($str){
	$gujdigit=array('૦','૧','૨','૩','૪','૫','૬','૭','૮','૯');
	$engdigit=array('0','1','2','3','4','5','6','7','8','9');
	$k=0;
	foreach($gujdigit as $gd){$str=str_replace($engdigit[$k],$gujdigit[$k],$str);$k++;}
	return $str;
}

function G2E($str){
	$gujdigit=array('૦','૧','૨','૩','૪','૫','૬','૭','૮','૯');
	$engdigit=array('0','1','2','3','4','5','6','7','8','9');
	$k=0;
	foreach($gujdigit as $gd){$str=str_replace($gujdigit[$k],$engdigit[$k],$str);$k++;}
	return $str;
}

function getGstType($gstin){
	$gstType = 3;
	if(!empty($gstin))
	{
		if(substr($gstin, 0, 2) != "24"):
			$gstType = 2;
		else:
			$gstType = 1;
		endif;
	}
	return $gstType;
}

function timeSum($times) {
    $hours = 0;$minutes = 0;$seconds = 0; //declare minutes either it gives Notice: Undefined variable
    // loop throught all the times
    foreach ($times as $time) {
        list($h, $m,$s) = explode(':', $time);
        $seconds += $h * 3600;
        $seconds += $m * 60;
        $seconds += $s;
    }
    // returns the time already formatted
    
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    
    //return sprintf('%02d:%02d:%02d', $hours, $minutes, $minutes);
}

function formatSeconds($seconds,$format="H:i:s") {
	$t = round($seconds);
	if($format=="H:i:s"):
	    return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
	elseif($format=="H:i"):
	    return sprintf('%02d:%02d', ($t/3600),($t/60%60), $t%60);
	else:
	    return sprintf('%02d', ($t/3600),($t/60%60), $t%60);
	endif;
}

/* Convert Time to Seconds */
function timeToSeconds($time = "00:00:00",$format = "H:i:s") {
	$time = explode(':', $time);
	$h = ($time[0] * 3600);
	$m = (isset($time[1]))?($time[1] * 60):0;
	$h = (isset($time[2]))?$time[2]:0;

	if($format == "H"):
		return $h;
	elseif($format == "H:i"):
		return $h + $m;
	else:
		return $h + $m + $s;
	endif;
}

/* Convert Seconds to Time */
function secondsToTime($seconds = 0,$format = "H:i:s") {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds - ($h * 3600) - ($m * 60);

	if($format == "H"):
		return sprintf('%02d', $h);
	elseif($format == "H:i"):
		return sprintf('%02d:%02d', $h, $m);
	else:
		return sprintf('%02d:%02d:%02d', $h, $m, $s);
	endif;
}

function addTimeToDate($date,$time,$dateFormat='Y-m-d H:i:s',$type="H"){
	$newDate = '';
	switch($type){
		case 'H': $newDate .= date($dateFormat,strtotime($date) + ($time * 3600));break;					
		case 'M': $newDate .=	date($dateFormat,strtotime($date) + ($time * 3600 * 60));break;					
		case 'S': $newDate .=	date($dateFormat,strtotime($date) + ($time * 3600 * 60 * 60));break;					
		case 'D': $newDate .=	date($dateFormat,strtotime($date) + ($time * 3600 * 24));break;					
	}		
	return $newDate;
}

function containsWord($str, $word){
	return !!preg_match('#\\b' . preg_quote($word, '#') . '\\b#i', $str);
}

function dateDiff($date1,$date2){
	$date1 = new DateTime($date1);
	$date2 = new DateTime($date2);

	$interval = $date1->diff($date2);

	$days = $interval->days;

	//$days = $interval->format('%r%a'); // Includes the sign (+/-)

	return $days;
}

/*** For Reindexing First Array from Second Array ***/
function first(&$arr, $low, $high, $x, $n){
	if ($high >= $low)
	{
		$mid = intval($low + ($high - $low) / 2);
		if (($mid == 0 || $x > $arr[$mid - 1]) && $arr[$mid] == $x){return $mid;}				
		if ($x > $arr[$mid]){return first($arr, ($mid + 1), $high, $x, $n);}
		return first($arr, $low, ($mid - 1), $x, $n);
	}
	return -1;
}

function sortAccording(&$A1, &$A2, $m, $n){
	$temp = array_fill(0, $m, NULL);
	$visited = array_fill(0, $m, NULL);
	for ($i = 0; $i < $m; $i++){$temp[$i] = $A1[$i];$visited[$i] = 0;}
	sort($temp);
	$ind = 0;		
 
	for ($i = 0; $i < $n; $i++)
	{
		$f = first($temp, 0, $m - 1, $A2[$i], $m);	 
		if ($f == -1) continue;	 
		for ($j = $f; ($j < $m && $temp[$j] == $A2[$i]); $j++){$A1[$ind++] = $temp[$j];$visited[$j] = 1;}
	}	 
	for ($i = 0; $i < $m; $i++){if ($visited[$i] == 0){$A1[$ind++] = $temp[$i];}}
}

function is_valid_gstin($gstin) {
	$regex = "/^([0][1-9]|[1-2][0-9]|[3][0-7])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/";
	return preg_match($regex, $gstin);
}

function sortDates( $arr, $tp='ASC' ) {if($tp=='ASC'){usort($arr, "date_sortA");}else{usort($arr, "date_sortD");} return $arr;}

function date_sortA( $a, $b ) {return strtotime($a) - strtotime($b);}

function date_sortD( $a, $b ) {return strtotime($b) - strtotime($a);}

function encodeURL($url){return urlencode(base64_encode(json_encode($url)));}

function decodeURL($url){return json_decode(base64_decode(urldecode($url)));}	

function moneyFormatIndia($num) {return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);}

function n2a($num){$alphabet = range('A', 'Z');array_unshift($alphabet , '');return $alphabet[(int)$num];}

function lpad($value,$totalPad=0,$padString='0'){return str_pad($value, $totalPad, $padString, STR_PAD_LEFT);}

function jpArraySum($arr,$matchKey,$sumKey){
	$result = [];
	foreach($arr as $v){
		$key = $v[$matchKey];
		if(isset($result[$key])){$result[$key][$sumKey] += $v[$sumKey];}else{$result[$key] = $v;}
	}
	return array_values($result);
}

function getFinDates($date){
    $startYear  = ((int)date("m",strtotime($date)) >= 4) ? date("Y",strtotime($date)) : (int)date("Y",strtotime($date)) - 1;
	$endYear  = ((int)date("m") >= 4) ? date("Y") + 1 : (int)date("Y");
	
	$fdates = Array();
	$fdates[] = date("Y-m-d",strtotime($startYear."-04-01"));
	$fdates[] = date("Y-m-d",strtotime($startYear."-03-31"));
	return array_values($fdates);
}

function n2y($value,$statYear = "2024") {
	$alphabet = range('A', 'Z'); return (!is_numeric($value)) ? (intVal(array_search($value, $alphabet)) + $statYear) : $alphabet[intVal($value)-$statYear] ;
}

function n2m($value) {$alphabet = range('A', 'Z'); return (!is_numeric($value)) ? (intVal(array_search($value, $alphabet))) : $alphabet[intVal($value)-1] ;}

function isVehicleNumber($Number){
	$pattern = "/^[a-zA-Z]{2}[0-9]{2}[a-zA-Z]{1,2}[0-9]{3,4}$/i";
	if (preg_match($pattern, $Number)){
	   return true;
	}else {
	   return false;
   }
}
	
function TO_FLOAT($string){return (float) filter_var( $string, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );}

function TO_STRING($string){return trim(ltrim((preg_replace('/[0-9]+/', '', $string)),'.'));}

function STR_BTW_CHAR($str,$btw){preg_match_all('#\\'.$btw[0].'(.*?)\\'.$btw[1].'#', $str, $match);return ((!empty($match[1])) ? $match[1] : '');}

function countDayInMonth($dayName,$date=""){    
    $date = (!empty($date))?date("Y-m",strtotime($date)):date("Y-m");
    $days = date("t",strtotime($date));
    $count = 0;
    for($i=1;$i<=$days;$i++):
        if(date("l",strtotime(date("Y-m-d",strtotime($date."-".$i)))) == ucwords($dayName)):
            $count += 1;
        endif;
    endfor;
    return $count;
}

// Find closest Date from Array
function findClosestDate($array, $date){
    //$count = 0;
    foreach($array as $day)
    {
        //$interval[$count] = abs(strtotime($date) - strtotime($day));
        $interval[] = abs(strtotime($date) - strtotime($day));
        //$count++;
    }

    asort($interval);
    $closest = key($interval);

    return $array[$closest];
}

function numberFormatIndia($num) {
	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);

	/* if(is_string($num) == true){
		$num = floatVal($num);
	}

	// Check if the number is negative
    $negative = false;
    if ($num < 0) {
        $negative = true;
		$num = abs($num);
    }
	
	$decimal = "";$number="";
	if(is_float($num) == true){ 
		$number = explode(".",$num); $num = $number[0]; $decimal = (isset($number[1]))?".".$number[1]:".00"; 
	}
	
	$explrestunits = "" ;
	if(strlen($num)>3) {
		$lastthree = substr($num, strlen($num)-3, strlen($num));
		$restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
		$restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
		$expunit = str_split($restunits, 2);
		for($i=0; $i<sizeof($expunit); $i++) {
			// creates each of the 2's group and adds a comma to the end
			if($i==0) {
				$explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
			} else {
				$explrestunits .= $expunit[$i].",";
			}
		}
		$thecash = $explrestunits.$lastthree.$decimal;
	} else {
		$thecash = $num.$decimal;
	}

	// Prepend the negative sign if the number was negative
    if ($negative) {
        $thecash = '-' . $thecash;
    }
	return $thecash; // writes the final format where $currency is the currency symbol. */
}

/* 
Created By : Milan Chauhan
Created At : 2023-03-28
Use : Get date formate financial year wise 
*/
function getFyDate($format = "Y-m-d",$date=""){
	$CI =& get_instance();
	$dates = explode(' AND ',$CI->session->userdata('financialYear'));

	$startYearDate = new DateTime($dates[0]);
	$endYearEnd = new DateTime($dates[1]);
	$interval = DateInterval::createFromDateString('1 month');
	$daterange = new DatePeriod($startYearDate, $interval ,$endYearEnd);

	$list=array();
	foreach($daterange as $row):
		$list[strval($row->format("m"))] = $row->format("m-Y");
	endforeach;

	$day = (!empty($date))?date('d',strtotime($date)):date('d');
	$month = (!empty($date))?date('m',strtotime($date)):date('m');
	$time = (!empty($date))?date('H:i:s',strtotime($date)):date('H:i:s');

	return date($format,strtotime($day.'-'.$list[strval($month)].' '.$time));
}

/*
Created By : Jaldeep Patel
Created At : 01-04-2023
Use : It Returns Current Financial Year as yy-yy Format
*/
function getShortFY(){return ( date('m') < 4) ? (date('y') - 1)."-".date('y') : date('y')."-".(date('y') + 1) ; }


/* Print Decimal Without 0 Precision */
function printDecimal($val){return number_format($val,0,'','');}

/* Ignore Single/Double Quote **/
function trimQuotes($val){return str_replace('"','\"',$val);}

/** Date Format **/
function formatDate($date,$format='d-m-Y'){return (!empty($date)) ? date($format,strtotime($date)) : '';}

/** GET PREFIX ARRAY **/
function getPrefix($prefix,$explodeBy = '/'){return explode($explodeBy,$prefix);}

/** GET NO WITH FORMATED PREFIX **/
function getPrefixNumber($prefix,$no,$explodeBy = '/'){ $prfx = explode($explodeBy,$prefix);return $prefix.$explodeBy.$no; }

/* Get Party List Options */
function getPartyListOption($partyList,$partyId = 0){
	$options = '';
	foreach($partyList as $row):
		$selected = (!empty($partyId) && $partyId == $row->id)?"selected":"";
		$options .= '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
	endforeach;

	return $options;
}

function getItemListOption($itemList,$itemId = 0){
	$options = '';
	foreach($itemList as $row):
		$selected = (!empty($itemId) && $itemId == $row->id)?"selected":"";
		$itemName = (!empty($row->item_code))?"[ ".$row->item_code." ] ".$row->item_name : $row->item_name;
		$options .= '<option value="'.$row->id.'" '.$selected.'>'.$itemName.'</option>';
	endforeach;

	return $options;
}

function getItemUnitListOption($unitList,$unit_name = 0){
	$options = '';
	foreach($unitList as $row):
		$selected = (!empty($unit_name) && $unit_name == $row->unit_name)?"selected":"";
		$options .= '<option value="'.$row->unit_name.'" data-unit="'.$row->unit_name.'" data-description="'.$row->description.'" '.$selected.'>[' . $row->unit_name . '] ' . $row->description . '</option>';
	endforeach;

	return $options;
}

function getHsnCodeListOption($hsnCodeList,$hsn = ""){
	$options = '';
	foreach($hsnCodeList as $row):
		$selected = (!empty($hsn) && $hsn == $row->hsn)?"selected":"";
		$options .= '<option value="'.$row->hsn.'" data-gst_per="'.floatVal($row->gst_per).'" '.$selected.'>'.$row->hsn.'</option>';
	endforeach;

	return $options;
}

/* Get Location List Options */
function getLocationListOption($locationList,$locationId = 0){
	$groupedStores = array_reduce($locationList, function($store, $location) {
		$store[$location->store_name][] = $location;
		return $store;
	}, []);
	
	$options = '';
	foreach ($groupedStores as $store => $location):
		$options .= '<optgroup label="' . $store . '">';
		foreach ($location as $row):
			$selected = (!empty($locationId) && $locationId == $row->id)?"selected":"";
			$options .= '<option value="' . $row->id . '" '.$selected.'>' . $row->location . '</option>';
		endforeach;
		$options .= '</optgroup>';
	endforeach;

	return $options;
}

/* Get Sales / Purchase Account Options */
function getSpAccListOption($accounts,$acc_id = 0){
	$options = '<option value="">Select Type</option>';
	foreach($accounts as $row):
		$selected = (!empty($acc_id) && $acc_id == $row->id)?"selected":"";
		$options .= '<option value="'.$row->id.'" data-tax_class="'.$row->system_code.'" '.$selected.'>'.$row->party_name.'</option>';
	endforeach;

	return $options;
}

/* Get TDS Class List */
function getTDSClassListOptions($tdsClassList,$tds_class_id = 0){
	$options = '';
	foreach($tdsClassList as $row):
		$selected = (!empty($tds_class_id) && $tds_class_id == $row->id)?"selected":"";
		$options .= '<option value="'.$row->id.'" data-class_type="'.$row->class_type.'" '.$selected.'>'.$row->class_name.'</option>';
	endforeach;

	return $options;
}

/* Get Tax Class Options */
function getTaxClassListOption($taxClassList,$tax_class_id = 0){
	$options = '<option value="">Select Type</option>';
	foreach($taxClassList as $row):
		$selected = (!empty($tax_class_id) && $tax_class_id == $row->id)?"selected":(($row->is_defualt == 1)?"selected":"");
		$options .= '<option value="'.$row->id.'" data-gst_type="'.$row->gst_type.'" data-sp_acc_id="'.$row->sp_acc_id.'" data-tax_class="'.$row->tax_class.'" '.$selected.'>'.$row->tax_class_name.'</option>';
	endforeach;

	return $options;
}

/* Get Year Prefix */
function getYearPrefix($format,$date=""){
	if(empty($date)){$date = date('Y-m-d');}
	$Y=intval(date('Y',strtotime($date)));
	$y=intval(date('y',strtotime($date)));
	$m=intval(date('m',strtotime($date)));
	if($format == 'NUMERIC_YEAR'){return $Y;} // 2024
	if($format == 'ALPHA_YEAR'){return n2y(date('Y',strtotime($date)),'2024');} // A 
	if($format == 'SHORT_YEAR'){ return ($m < 4) ? ($y - 1)."-".$y : $y."-".($y + 1) ; } // 24-25
	if($format == 'LONG_YEAR'){ return ( $m < 4) ? ($Y - 1)."-".$y : $Y."-".($y + 1) ; } // 2024-25
}


function split_contact($contact) {
    $contacts = array();
    foreach ($contact as $key => $values) {
        $key = strtolower(preg_replace(array('/\(s\)$/', '/\s+/'), array('', '_'), $key));
        foreach (explode(',', $values) as $index => $value) {
            $contacts[$index][$key] = trim($value);
        }
    }
    return $contacts;
}

function buildTree($data, $currentParent,$lvl_str = '',$lvl = 1,$itmLevel = [])
{
	foreach ($data as $key => &$item) {
		$rowBg = '';
		if(!isset($itmLevel[$item['parent_id']]['lvl']))
		{
			$itmLevel[$item['parent_id']]['lvl'] = $lvl;
			$itmLevel[$item['parent_id']]['lvl_str'] = $lvl_str.'.1';
			
			$itmLevel[$item['parent_id'].'@'.$itmLevel[$item['parent_id']]['lvl']] =1;
			
			$lvl++;
		}
		else
		{
			if(isset($itmLevel[$item['parent_id']]['lvl'])){
				$itmLevel[$item['parent_id'].'@'.$itmLevel[$item['parent_id']]['lvl']]++;
			}
		}
		$itmLevel[$item['parent_id']]['lvl_str'] = $lvl_str.'.'.$itmLevel[$item['parent_id'].'@'.$itmLevel[$item['parent_id']]['lvl']];
		
		$pd = (!empty($itmLevel[$item['parent_id']]['lvl']) ? ($itmLevel[$item['parent_id']]['lvl'] * 20) : 0);
		
		
		echo '<tr class="'.$rowBg.'">';
			echo '<td>'.$itmLevel[$item['parent_id']]['lvl_str'].'</td>';
			echo '<td class="text-left" style="padding-left:'.$pd.'px!important;">'.$item['text'].'</td>';
			echo '<td class="text-right">'.$item['qty'].'</td>';
		echo '</tr>';
		
		
		if (isset($item['nodes'])) {
			$currentParent = $item['id'];
			buildTree($item['nodes'], $currentParent,$itmLevel[$item['parent_id']]['lvl_str'],$lvl,$itmLevel);
		}
	}
}

function buildTreeView($data, $currentParent)
{
	foreach ($data as $key => &$item) {
		echo '<ul>';
		echo '<li>';
			echo '<div class="listree-submenu-heading">' . $item['parent_id'] . '@' . $item['id'] . '@' . $item['text'] . '</div>';
		echo '</li>';
		
		if (isset($item['nodes'])) {
			$currentParent = $item['id'];
			buildTree($item['nodes'], $currentParent);
		}

		echo '</ul>';
	}
}

function buildTreeForCost($data, $currentParent,$lvl_str = '',$lvl = 1,$itmLevel = [])
{
	foreach ($data as $key => &$item) {
		$rowBg = '';
		if(!isset($itmLevel[$item['parent_id']]['lvl']))
		{
			$itmLevel[$item['parent_id']]['lvl'] = $lvl;
			$itmLevel[$item['parent_id']]['lvl_str'] = $lvl_str.'.1';
			
			$itmLevel[$item['parent_id'].'@'.$itmLevel[$item['parent_id']]['lvl']] =1;
			
			$lvl++;
		}
		else
		{
			if(isset($itmLevel[$item['parent_id']]['lvl'])){
				$itmLevel[$item['parent_id'].'@'.$itmLevel[$item['parent_id']]['lvl']]++;
			}
		}
		$itmLevel[$item['parent_id']]['lvl_str'] = $lvl_str.'.'.$itmLevel[$item['parent_id'].'@'.$itmLevel[$item['parent_id']]['lvl']];
		
		$pd = (!empty($itmLevel[$item['parent_id']]['lvl']) ? ($itmLevel[$item['parent_id']]['lvl'] * 20) : 0);
		$gross_weight="";$net_weight="";$scrap_rate="";$final_rate = 0;
		$purchaseCost =  0;
        $process_cost =(!empty($item['process_cost']))?$item['process_cost']*$item['qty']:0;
		if($item['item_type'] == 3){
			$gross_weight =(!empty($item['qty']))?$item['qty']:0;
			$net_weight =(!empty($item['net_weight']))?$item['net_weight']:0;
			$scrap_rate =(!empty($item['scrap_rate']))?$item['scrap_rate']:0;
			$total_mt_rate = $gross_weight*$item['purchase_price'];
			$scrap_rate = $scrap_rate * ($gross_weight - $net_weight);
			$final_rate = $total_mt_rate - $scrap_rate;
		}else{
			$purchaseCost =  (!empty($item['purchase_price']))?$item['purchase_price']*$item['qty']:0;
		}
        $amount = $purchaseCost + $process_cost+$final_rate;
		echo '<tr class="'.$rowBg.'">';
			echo '<td>'.$itmLevel[$item['parent_id']]['lvl_str'].'</td>';
			echo '<td class="text-left" style="padding-left:'.$pd.'px!important;">'.$item['text'].'</td>';
			echo '<td class="text-right">'.$item['qty'].'</td>';
			echo '<td class="text-right">'.$purchaseCost.'</td>';
			echo '<td class="text-right">'.$process_cost.'</td>';
			echo '<td class="text-right">'.$net_weight.'</td>';
			echo '<td class="text-right">'.$gross_weight.'</td>';
			echo '<td class="text-right">'.$scrap_rate.'</td>';
			echo '<td class="text-right">'.$amount.'</td>';
		echo '</tr>';
		
		
		if (isset($item['nodes'])) {
			$currentParent = $item['id'];
			buildTreeForCost($item['nodes'], $currentParent,$itmLevel[$item['parent_id']]['lvl_str'],$lvl,$itmLevel);
		}
	}
}

?>
