<?php

$compress = 1;
$encode = 1;
$decode = 0;
$verbose = 0;
$folder = 'src';
$except_dirs = ['resources', 'config', 'bootstrap', 'node_modules', 'public', 'storage', 'tests', 'vendor']; // example
$except_files = ['app/SiteModel.php']; // 1/example.php

function research($path, $pattern) {
    $dir_iterator = new RecursiveDirectoryIterator($path);
	$iterator = new RecursiveIteratorIterator($dir_iterator);
	$files = new RegexIterator($iterator, '/'.$pattern.'/', RegexIterator::GET_MATCH);
    $fileList = [];
	foreach ($files as $file) {
		$fileList = array_merge($fileList, $file);
	}
	
	return $fileList;
}

function xor_this($text) {
    $key = 'a';

    // Our output text
    $output = '';

    // Iterate through each character
    //for($i=0; $i<strlen($text); )
    //{
    //    for($j=0; ($j<strlen($key) && $i<strlen($text)); $j++,$i++)
    //    {
    //        $output .= $text{$i} ^ $key{$j};
    //    }
    //}
	// Iterate through each character
    for($i=0; $i<strlen($text); $i++)
    {
        $output .= $text{$i} ^ $key;
    }
    return $output;
}

function shift_cipher($text, $num){
	$text_array = str_split($text);
	$shifted = '';
	foreach($text_array as $char){
		$shifted .= chr(ord($char) + $num);
	}
	return $shifted;
}

function force_file_put_contents($filepath, $text){
	//this function makes directory if not exists
    try {
        $isInFolder = preg_match("/^(.*)\\". DIRECTORY_SEPARATOR ."([^\\". DIRECTORY_SEPARATOR ."]+)$/", $filepath, $filepathMatches);
        if($isInFolder) {
            $folderName = $filepathMatches[1];
            $fileName = $filepathMatches[2];
            if (!is_dir($folderName)) {
                mkdir($folderName, 0777, true);
            }
        }
        file_put_contents($filepath, $text);
    } catch (Exception $e) {
        echo "ERR: error writing to '$filepath', ". $e->getMessage();
    }
}

function rrmdir($dir) { 
	if (is_dir($dir)) { 
		$objects = scandir($dir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir. DIRECTORY_SEPARATOR .$object))
					rrmdir($dir. DIRECTORY_SEPARATOR .$object);
				else
					unlink($dir. DIRECTORY_SEPARATOR .$object); 
			}
		}
		rmdir($dir); 
	} 
 }

///////////////////////////////////
//        Get all files          //
///////////////////////////////////

// Correct directory separator differences for except_dirs array
$ds_corrected_dirs = [];
foreach($except_dirs as $except_dir){
	// DIRECTORY_SEPARATOR correction
	$ds_corrected_dir = str_replace('/', '\\' . DIRECTORY_SEPARATOR, $except_dir); // example\/e\/b
	$ds_corrected_dir = $folder . '\\' . DIRECTORY_SEPARATOR . $ds_corrected_dir; // src\/example\/e\/b
	$ds_corrected_dirs[] = $ds_corrected_dir;
}

$pattern = '^((?!' . implode('|', $ds_corrected_dirs) . ').+\.php)';
$php_files = research($folder, $pattern);
$files = [];
foreach($php_files as $file_path)
{
	if($file_path == $folder . DIRECTORY_SEPARATOR . '.php')
	{
		continue;
	}
	
	//exception dirs
	foreach($except_dirs as $except_dir)
	{
		if(substr($except_dir, -1) != DIRECTORY_SEPARATOR){
			$except_dir .= DIRECTORY_SEPARATOR;
		}
		//if directory is in exceptions
		if(substr($file_path, 0, strlen($except_dir)) == $except_dir)
		{
			continue(2);
		}
	}
	//exception files
	if(in_array(str_replace('src/', '', str_replace('\\', '/', $file_path)), $except_files))
	{
		continue;
	}
	
	array_push($files, $file_path);
}

//delete output dir contents
rrmdir('output');
mkdir('output', 0777, true);

foreach($files as $file)
{
	//////////////////////////////////
	//           Compress           //
	//////////////////////////////////

	if($compress){
		$text = ltrim(php_strip_whitespace($file));
	}else{
		$text = ltrim(file_get_contents($file));
	}
	//remove php tag
	if(substr($text, 0, 5) == '<?php'){
		$text = ltrim(substr($text, 5));
	}else{
		$text = ' ?>' . $text;
	}

	//////////////////////////////////
	//           Encode             //
	//////////////////////////////////

	if($encode){
		//base64
		//$base64 = base64_encode($text);

		//shift + 3
		$shifted_base64 = shift_cipher($text, 3);

		//xor
		$xor_shifted = xor_this($shifted_base64);
		
		$result = $xor_shifted;
		//base64
		//$base64_xor = $result = base64_encode($xor_shifted);

		
		if($verbose){
			echo "Encoded: \n\n";
			echo $result;
			echo "\n____________________\n\n";
		}
		
		force_file_put_contents('output' . substr($file, strlen($folder)), "<?php\nycsm_run(base64_decode('".base64_encode($result)."'));");
		echo '. '; flush();
	}
}

//////////////////////////////////
//           Decode             //
//////////////////////////////////

if($decode){
	$base64_decoded = base64_decode($result);
	$xor_base64_decoded = xor_this($base64_decoded);
	$unshift_xor = shift_cipher($xor_base64_decoded, -3);

	echo "Decoded: \n\n";
	echo base64_decode($unshift_xor);
}

echo "\n\nDone.";
