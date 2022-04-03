<?php

if ($argc < 3) {
    echo "You should pass exactly 2 files!\n";
    die();
}
if (!is_file($argv[1]) || !is_file($argv[2])) {
    echo "Invalid file paths!\n";
    die();
}

$outputDir = $argv[3] ??  __DIR__ .'/output';

$baseFile = file_get_contents($argv[1]);
$configFile = file_get_contents($argv[2]);



$baseFileData = json_decode($baseFile);
$configFileData = json_decode($configFile);

clearDir(__DIR__ .'/var');
loopThroughParamsObject($configFileData, $baseFileData);
clearDir($outputDir);
copyFiles(__DIR__ .'/var', $outputDir);

echo "Files created\n";

function loopThroughParamsObject(stdClass $paramsArray, stdClass $baseParamsArray, int $i = 0, ?stdClass $base = null)
{
    if ($base == null) {
        $base = $baseParamsArray;
    }
    foreach ($paramsArray as $key => $item) {
        if (!isset($baseParamsArray->{$key})) {
            echo "Invalid config file data, key not exist!\n";
            die();
        }
        if (is_object($item)) {
            loopThroughParamsObject($item, $baseParamsArray->{$key}, $i, $base);
        } else {
            foreach ($item as $singleConfig) {
                $prev = $baseParamsArray->{$key};
                $baseParamsArray->{$key} = $singleConfig;
                $fi = new FilesystemIterator(__DIR__ . "/var/", FilesystemIterator::SKIP_DOTS);
                file_put_contents(__DIR__ . "/var/output_file_".iterator_count($fi).".json", json_encode($base));
                $baseParamsArray->{$key} = $prev;
            }
        }
        $i++;
    }
}

function clearDir(string $dir)
{
    if (!is_dir($dir)) {
        mkdir($dir);
    } else {
        array_map('unlink', array_filter((array)glob($dir.'/*')));
    }
}

function copyFiles(string $src, string $dst)
{
    $fi = new FilesystemIterator($src, FilesystemIterator::SKIP_DOTS);
    foreach ($fi as $file) {
        copy($file->getPathname(), $dst.'/'.$file->getFilename());
    }
}


