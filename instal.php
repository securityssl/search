<?PHP
	/*
		Powered by minibam
		* https://vk.com/nikita_kechenkov
	*/
	set_time_limit(0);
	ignore_user_abort(1);
	
	function download_file($urlDownload = "") {
		if(!file_exists("temp"))
			mkdir('temp');
		
		if(file_exists("./temp/patch.zip"))
			return true;
		
		$curlInit = curl_init($urlDownload);
		$fOpen = fopen("./temp/patch.zip", "wb");
		curl_setopt($curlInit, CURLOPT_FILE, $fOpen);
		curl_setopt($curlInit, CURLOPT_HEADER, 0);
		curl_exec($curlInit);
		curl_close($curlInit);
		
		return fclose($fOpen);
	}
	
	function recursiveRemoveDir($dir) {
		$includes = new FilesystemIterator($dir);
		
		foreach($includes as $include) {
			if(is_dir($include) && !is_link($include)) {
				recursiveRemoveDir($include);
			}
			else unlink($include);
		}
		
		rmdir($dir);
	}
	
	function edit_file($sFile) {
		$iContext = file_get_contents($sFile);
		
		$domain = $_SERVER['HTTP_HOST'];
		$iResult = explode('.', $domain, 2);
		
		$iContext = str_replace('{site:name}', $iResult['0'], $iContext);
		$iContext = str_replace('{domain:name}', $iResult['1'], $iContext);
		
		file_put_contents($sFile, $iContext);
	}
	
	if(download_file(file_get_contents("https://awscode.ru/gamecms/download/optimal"))) {
		$zipArchive = new ZipArchive;
		$resourcePatch = $zipArchive->open("./temp/patch.zip");
		
		if($resourcePatch === true) {
			$zipArchive->extractTo("./");
			if($zipArchive->close()) {
				edit_file("{$_SERVER['DOCUMENT_ROOT']}/.htaccess");
				edit_file("{$_SERVER['DOCUMENT_ROOT']}/robots.txt");
				edit_file("{$_SERVER['DOCUMENT_ROOT']}/inc/config.php");
				
				recursiveRemoveDir("temp");
				
				exit("<script language='JavaScript' type='text/javascript'>window.location.replace('/')</script>");
			}
		}
		else {
			exit('Error install: [Смените версию PHP / <a href="https://vk.com/awscode" target="_blank">Обратитесь к технической поддержке AWS CODE</a> или обратитесь к <a href="https://vk.com/nikita_kechenkov" target="_blank">Никите</a>]');
		}
	}
?>