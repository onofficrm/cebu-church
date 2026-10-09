<?php
/*******************************************************************************
** 변수 설정
*******************************************************************************/
$mode = "s"; // 모드 : s(설치), r(재설치), d(삭제) 
$tar_file = "./1.tar.gz"; // 압축파일명
$is_folder = FALSE; // 폴더 설치 여부

// 파라미터
if(isset($_GET['m'])) $mode = $_GET['m'];
if(isset($_GET['f'])) {
	$folder = '/'.$_GET['f'];
	$is_folder = TRUE;
}



// common.php 발췌
function g5_path()
{
    $chroot = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], dirname(__FILE__))); 
    $result['path'] = str_replace('\\', '/', $chroot.dirname(__FILE__)); 
    $server_script_name = preg_replace('/\/+/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])); 
    $server_script_filename = preg_replace('/\/+/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'])); 
    $tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $server_script_name); 
    $document_root = str_replace($tilde_remove, '', $server_script_filename); 
    $pattern = '/.*?' . preg_quote($document_root, '/') . '/i';
    $root = preg_replace($pattern, '', $result['path']); 
    $port = ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':'.$_SERVER['SERVER_PORT']; 
    $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://'; 
    $user = str_replace(preg_replace($pattern, '', $server_script_filename), '', $server_script_name); 
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']; 
    if(isset($_SERVER['HTTP_HOST']) && preg_match('/:[0-9]+$/', $host)) 
        $host = preg_replace('/:[0-9]+$/', '', $host); 
    $host = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*]/", '', $host); 
    $result['url'] = $http.$host.$port.$user.$root; 
    return $result;
}

$g5_path = g5_path();
$g5     = array();


// lib/common.lib.php 발췌


// 설정경로
$set_path = $g5_path['path'].$folder;

if($mode == 'd' || $mode == 'r'){ // 삭제, 리셋일 경우

	// 폴더 생성 - 폴더가 없는 경우 에러 방지를 위해 생성
	if($is_folder){
		@mkdir($set_path, 0755);
		@chmod($set_path, 0755);
	}
	
	// 압출풀기
	@exec("tar -xzf $tar_file -C " . $set_path);

	// data 생성 퍼미션 707
	@mkdir($set_path.'/data', 0707);
	@chmod($set_path.'/data', 0707);
	
	// 압출파일 목록
	@exec("tar -tf $tar_file", $set_list);

	// 제귀적 삭제 함수
	function delete_dir($path) {
		
		global $set_path, $set_list;

		@chmod($path,0777);
		$directory = dir($path);
		while($entry = $directory->read()) {

			// data폴더 내용이 있는 경우라도 강제 삭제 ---> || 이후 조건

			if(in_array(str_replace($set_path.'/', '', (is_dir($path."/".$entry))? $path."/".$entry."/" : $path."/".$entry), $set_list) || substr(str_replace($set_path.'/', '', (is_dir($path."/".$entry))? $path."/".$entry."/" : $path."/".$entry), 0, 5) == "data/" ) {

				if($entry != "." && $entry != "..") {

					if(is_dir($path."/".$entry)) {
						delete_dir($path."/".$entry);
					}
					else {
						@chmod($path."/".$entry,0777);
						@UnLink ($path."/".$entry);
					}
				}
			} 
		}

		$directory->close();
		if($path != $set_path) @rmdir($path); // 최상위 폴더 외 삭제
	}

	delete_dir($set_path); // 폴더, 파일 삭제

	// data 폴더 삭제
	@chmod($set_path.'/data',0777);
	@rmdir($set_path.'/data');
	
	// 폴더가 있는 경우 해당 폴더 삭제
	if($is_folder){
		@chmod($set_path,0777);
		@rmdir($set_path);
	}
	
	// 테이블 삭제
	$sql = "SELECT CONCAT('DROP TABLE ' , TABLE_NAME) as drop_table FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '".G5_TABLE_PREFIX."%'";
	$result = sql_query($sql);
	while ($sql = sql_fetch_array($result)) {
		sql_query($sql['drop_table']);
	}

}

if($mode == 's' || $mode == 'r'){ // 설치, 리셋인 경우

	// 폴더 생성
	if($is_folder){
		@mkdir($set_path, 0755);
		@chmod($set_path, 0755);
	}
	
	// data 폴더 생성
	@exec("tar -xzf $tar_file -C " . $set_path);
	@mkdir($set_path.'/data', 0707);
	@chmod($set_path.'/data', 0707);
}
?>
