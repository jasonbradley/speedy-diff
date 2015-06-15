<?php

require_once 'SpeedyDiff.php';

use JasonBradley\SpeedyDiff\SpeedyDiff;

$file1 = './TestDiffFiles/file1.txt';
$file2 = './TestDiffFiles/file2.txt';

$speedDiff = new SpeedyDiff($file1, $file2);

echo $speedDiff->getDiffOutput();
