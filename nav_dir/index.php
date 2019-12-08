<?php

/**
 * @param $size - in bytes
 * @return string - size in kb, mb, gb
 */
function convertSize($size)
{
    $units = 'B';
    $new_size = $size;
    if ($new_size > 1024) {
        $new_size = $new_size / 1024;
        $units = 'KB';
    }
    if ($new_size > 1024) {
        $new_size = $new_size / 1024;
        $units = 'MB';
    }
    if ($new_size > 1024) {
        $new_size = $new_size / 1024;
        $units = 'GB';
    }
    return round($new_size, 2) . ' ' . $units;
}

function sortFilesAndDirs($dirs, $path)
{
    $directories = array();
    $files = array();
    foreach ($dirs as $key => $value) {
        if (is_file($path . $value)) {
            $files[] = $dirs[$key];
        } else {
            $directories[] = $dirs[$key];
        }
    }
    //natcasesort - sort an array using a case insensitive algorithm
    natcasesort($directories);
    natcasesort($files);
    return array_merge($directories, $files);
}

function filter_input_($name, $default)
{
    $result = $default;
    if (isset($_POST[$name])) {
        $result = $_POST[$name];
    }
    if (isset($_SERVER[$name])) {
        $result = $_SERVER[$name];
    }
    if (isset($_FILES[$name])) {
        $result = $_FILES[$name];
    }
    if (isset($_GET[$name])) {
        $result = $_GET[$name];
    }
    return $result;
}

/**
 * @param $dir - directory(if is dir)
 * @return array - content(files & dirs)
 */
function openDirectory($dir)
{
    $inner_dirs = array();
    if (is_dir($dir)) {
        if ($d = opendir($dir)) {
            while (($file = readdir($d)) !== false) {
                $inner_dirs[] = $file;
            }
            closedir($d);
        }
    }
    return $inner_dirs;
}

/**
 * @param $dirs - array of directories
 * @param $path - path to file or dir
 * @return array - creation dates
 */
function getFileDirDate($dirs, $path)
{
    $file_date = array();
    foreach ($dirs as $key => $value) {
        $file_date[$key] = date("Y.m.d _ H:i:s", filectime($path . $value));
    }
    return $file_date;
}

function getFileSize($dirs, $path)
{
    $file_size = array();
    foreach ($dirs as $key => $value) {
        if (is_file($path . $value))
            $file_size[$key] = convertSize(filesize($path . $value));
        else
            $file_size[$key] = ' - ';
    }
    return $file_size;
}

function getFileDirType($dirs, $path)
{
    $file_type = array();
    foreach ($dirs as $key => $value) {
        $file_type[$key] = filetype($path . $value);
    }
    return $file_type;
}

$dirs = array();
$file_size = array();
$file_type = array();
$file_date = array();

if (filter_input_("get_request", 'no_request') == 'no_request') {
    $dir = filter_input_("DOCUMENT_ROOT", '');
    $path = '../';
} else {
    $dir = filter_input_("get_request", '');
    $path = $dir;
}
$dirs = openDirectory($path);
$dirs = sortFilesAndDirs($dirs, $path);
$file_type = getFileDirType($dirs, $path);
$file_size = getFileSize($dirs, $path);
$file_date = getFileDirDate($dirs, $path);
$file = filter_input_('input_submit', '');
if (!empty($file)) {
    $loaded_file = filter_input_('file', '');
    $file_name = $loaded_file['name'];
    $file_tmp_location = $loaded_file['tmp_name'];
    $file_store = "$path/animation/". $file_name;
    move_uploaded_file($file_tmp_location, $file_store);
    //header("Refresh:0");
}
include "../general/header.php"; ?>
    <div class="right-col">
    <div class="news-info">
        <a href="">
            <div class="news">
                Navigator
            </div>
        </a>
        <div class="date">
            30 февраля 1313
        </div>
    </div>
    <div class="text-content  clearfix">
        <div id="registered">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($dirs as $key => $value) {
                    $path_get = '../' . $value . '/';
                    if (is_file($path . $value))
                        echo "<tr><td>$value</td>";
                    else
                        echo "<tr><td><a href='index.php?get_request=$path_get'>$value</a></td>";
                    echo "<td>$file_type[$key]</td>";
                    echo "<td>$file_size[$key]</td>";
                    echo "<td>$file_date[$key]</td></tr>";
                } ?>
            </table>
        </div>
        <form action="index.php" method="post" enctype="multipart/form-data">
            <input type="file" class="submit" name="file">
            <input type="submit" class="submit" name="input_submit" value="Load to current directory">
        </form>
    </div>

<?php
include "../general/footer.php";
