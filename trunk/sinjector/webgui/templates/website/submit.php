<br><center>
<?php
try {
    
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['upfile']['error']) ||
        is_array($_FILES['upfile']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    // Check $_FILES['upfile']['error'] value.
    switch ($_FILES['upfile']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    // You should also check filesize here. 
    if ($_FILES['upfile']['size'] > 1400000) {
        throw new RuntimeException('FileSize Limit exceeded.');
    }

	/*
    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['upfile']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }
	*/

	if(!checkMZ($_FILES['upfile']['tmp_name'])) throw new RuntimeException('Invalid file (not a valid windows EXEcutable)');

    // You should name it uniquely.
    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
	$ext="bin";
	$md5=md5_file($_FILES['upfile']['tmp_name']);
    if (!move_uploaded_file(
        $_FILES['upfile']['tmp_name'],
        sprintf('./uploads/%s.%s',
            $md5,
            $ext
        )
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }
    echo '<h3>Sample uploaded successfully</h3>';
?>
<br>
You can <a href=?analyze=<?=$md5?>>Analyze</a> this sample now, or Go <a href="?home">back</a>
<?
} catch (RuntimeException $e) {

    echo $e->getMessage();
	echo '<br><br>Go <a href="?home">back</a>';
}

function checkMZ($filename){
	$file=fopen($filename,"rb");
	$bytes=fread($file,2);
	fclose($file);
	if($bytes[0]=="M" && $bytes[1]=="Z") return true;
	return false;
}
?>

