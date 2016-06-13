<?php
/**
 * read a file and return an array include file name
 * @param string $filename
 * @return mixed an array or faile message
 */
function getFile($fileName)
{
	if($file = file_get_contents($fileName)){
		return explode('.',$fileName)+explode("\n", $file);
	}else{
		return 'open file faile';
	}
}

/**
 * connect to database
 * @param string $dbname database name
 * @return resources a sql resources
 */
function connectDb($dbname)
{
	$sql = new mysqli(DBHOST,DBUSER,DBPW);
	$sql->select_db($dbname);
	return $sql;
};

/**
 * return a sql insert staement
 * @param array $file by $getFile();
 * @return string
 */
function wordnet($file){
	$indexStatements = "INSERT INTO `index`(`lemma`, `pos`, `synset_cnt`, `ptr_cnt`, `ptr`, `sense_cnt`, `tagsense_cnt`, `offset`) VALUES";
	$dataStaements = "INSERT INTO `data`(`offset`, `lex_filenum`, `ss_type`, `w_cnt`, `word`, `p_cnt`, `ptr`, `f_cnt`, `frames`, `definition`, `sentence`) VALUES";

	$lineCount = count($file)-1;
	$value='';
	for($i=29;$i<$lineCount;$i++){
		if($file[0]=='index'){
			$value.=index(trim($file[$i]));
			$values = ($lineCount-1==$i)?$indexStatements.$value:'';
		}else if($file[0]=='data'){
			$value.=data(trim($file[$i]));
			$values = ($lineCount-1==$i)?$dataStaements.$value:'';
		}
	}
	return trim($values,',');
};

/**
 * return index value
 * @param string $line a line of index.* file
 * @return string
 */
function index($line)
{
	global $sql;
	$one=explode(' ',$line);
	
	$lemma = $sql->escape_string($one[0]);
	
	$pos = $one[1];
	
	$synset_cnt = $one[2];
	
	$ptr_cnt = $one[3]*1;
	
	$ptr = '';
	for($i=0;$i<$ptr_cnt;$i++){
		$ptr .= '"'.$one[$i+4].'",';
	}
	$ptr = $ptr!=''?$sql->escape_string('['.trim($ptr,',').']'):null;
	
	$sense_cnt = $one[$ptr_cnt+4]*1;
	
	$tagsense_cnt = $one[$ptr_cnt+5]*1;
	
	$offset = '';
	for($i=0;$i<$synset_cnt;$i++){
		$offset .='"'.$one[$i+6+$ptr_cnt].'",';
	}
	$offset = '['.trim($offset,',').']';
	
	$value = "('$lemma','$pos','$synset_cnt','$ptr_cnt','$ptr','$sense_cnt','$tagsense_cnt','$offset'),";
	
	return $value;
}

/**
 * return data value
 * @param $line a line of data.* file
 * @return string 
 */
function data($line)
{
	global $sql;
	$one = explode('|',$line);
	$data = explode(' ',$one[0]);
	
	$offset = $data[0];
	
	$lex_filenum = $data[1]*1;
	
	$ss_type = $data[2];
	
	$w_cnt = hexdec($data[3]);
	
	$word_max = $w_cnt*2+3;// $w_cnt*2-1+4
	$word = array_slice($data,4,$w_cnt*2);
	$word = array_chunk($word,2);
	/* just get word , remove lex_id
	$countWord = count($word);$w='';
	for($i=0;$i<=$countWord;$i+=2){
		if($i==$countWord)
			break;
		$w[]=$word[$i];
	}
	*/
	$word = $sql->escape_string(json_encode($word));
	
	$p_cnt = $data[$word_max+1]*1;
	
	$ptr="";
	if($p_cnt!=0){
		$ptr = array_slice($data,$word_max+2,$p_cnt*4);
		$ptr = array_chunk($ptr,4);
		$ptr = json_encode($ptr);
	}
	
	$frames = "";
	$f_cnt = $data[$w_cnt*2+$p_cnt*4+5]; //$w_cnt*2+4+$p_cnt*4+1
	$f_cnt = count($f_cnt)!=1?$f_cnt:'null';
	if($f_cnt!=0){
		$frame = array_slice($data,$w_cnt*2+$p_cnt*4+6,$f_cnt*3);
		$countFrame = count($frame);
		for($i=0;$i<$countFrame;$i+=3){
			$frames[]=($i<$countFrame)?array_merge([$frame[$i+1]],[$frame[$i+2]]):'';
		}
		$frames = json_encode($frames)."\n";
	}
	
	$gloss = explode(';',trim($one[1]));
	$definition = '';
	$sentence = '';
	$countGloss = count($gloss);
	for($j=0;$j<$countGloss;$j++){
		$gloss[$j] = trim($gloss[$j]);
		/* a little bug; so ...
		if(preg_match('#^[\"]#',trim($gloss[$j]))){
			$sentence[]=$gloss[$j];
		}else{
			$definition=$gloss[$j];
		}
		*/
		//$j==0?$definition=$gloss[$j]:$sentence[]=$gloss[$j]; //a little bug too.. like offset='09560255'
		preg_match('#"(.*)"#', $gloss[$j])?$sentence[]=$gloss[$j]:$definition[]=$gloss[$j];
	}
	$definition = $sql->escape_string(json_encode($definition));
	$sentence = $sentence==""?null:$sql->escape_string(json_encode($sentence));
	
	$value = "('$offset', $lex_filenum, '$ss_type', $w_cnt, '$word', $p_cnt, '$ptr', $f_cnt, '$frames', '$definition','$sentence'),";
	return $value;
}