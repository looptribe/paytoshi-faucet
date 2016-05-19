<?php

echo 'Paytoshi Faucet Script Builder' . PHP_EOL;

$diff = array();
exec('git status --porcelain', $diff);
if (!empty($diff)) {
    error_log('There are uncommited changes, please stash them before executing the build script.');
    exit(1);
}

$root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
$outPath = $root . 'dist' . DIRECTORY_SEPARATOR;

$composer = file_get_contents("${root}composer.json");
if ($composer === false) {
    throw new Exception("Cannot read ${root}composer.json");
}
$composerJson = json_decode($composer, true);
if ($composerJson === false) {
    throw new Exception("Cannot parse json ${root}composer.json");
}

$version = $composerJson['version'];

$out = "${outPath}paytoshi-faucet-v${version}.zip";

if (file_exists($out)) {
    echo "Warning: output file $out already exists." . PHP_EOL;
    unlink($out);
}

if (!file_exists($outPath) && mkdir($outPath, 0755, true) === false) {
    throw new Exception("Cannot create directory $outPath");
}

echo "Writing out file: $out" . PHP_EOL;

chdir($root);
exec(sprintf('zip -r %s . -x .git\* .idea\* coverage\* tests\* deployment\* phpunit.xml.dist bin\* dist\* .travis.yml',
    escapeshellarg($out)));

echo 'Done.' . PHP_EOL;
