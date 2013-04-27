<?php

require_once 'inc/autoload.php';


if (!isset($_POST["file"]) || !is_string($_POST["file"]))
{
    sendError("No file uploaded");
}

try {
    $analyzeImage = \Image\Image::createFromDataUrl($_POST["file"]);
    $start = microtime(true);
    $analyzer = new Analyzer($analyzeImage);
    $result = $analyzer->getResult();
    $duration = microtime(true) - $start;

    sendResponse(array(
        "result" => array(
            "background" => $result->background->getHexString(),
            "title"      => $result->title->getHexString(),
            "songs"      => $result->songs->getHexString(),
        ),
        "metrics" => array(
            "duration"         => sprintf("%f", $duration)."s",
            "memory"           => human_filesize(memory_get_usage()),
            "memory_real"      => human_filesize(memory_get_usage(true)),
            "memory_peak"      => human_filesize(memory_get_peak_usage()),
            "memory_peak_real" => human_filesize(memory_get_peak_usage(true)),
        )
    ));
}
catch (Exception $e)
{
    sendError($e);
}




/**
 * Sends an error and exits
 *
 * @param $message
 */
function sendError ($message)
{
    sendResponse(array(
        "error" => $message
    ));
}


/**
 * Sends data and exists
 *
 * @param $data
 */
function sendResponse ($data)
{
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}


/**
 * Renders the file size as human readable
 *
 * (thanks to: http://www.php.net/manual/de/function.filesize.php#106569)
 *
 * @param $bytes
 * @param int $decimals
 *
 * @return string
 */
function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = (int) floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}