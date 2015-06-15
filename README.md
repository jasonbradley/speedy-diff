speedy-diff ![alt tag](https://travis-ci.org/jasonbradley/speedy-diff.svg?branch=master)
===========

Compare two files with diff and create a file from the difference.

Installation via Composer:

composer require jasonbradley/speedy-diff:dev-master

Usage:

$speedyDiff = new \JasonBradley\SpeedyDiff\SpeedyDiff($file1, $file2);

echo $speedyDiff->getDiffOutput();
