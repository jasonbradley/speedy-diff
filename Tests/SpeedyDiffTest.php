<?php

require_once dirname(__FILE__) . '/../SpeedyDiff.php';

use JasonBradley\SpeedyDiff\SpeedyDiff,
    JasonBradley\SpeedyDiff\Exceptions\DiffException;

class SpeedyDiffTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @expectedException JasonBradley\SpeedyDiff\Exceptions\DiffException
     */
    public function testValidateFiles()
    {
        $speedyDiff = new SpeedyDiff(null, null);
    }

    public function testSpeedyDiff()
    {
        $file1 = dirname(__FILE__) . '/../TestDiffFiles/file1.txt';
        $file2 = dirname(__FILE__) . '/../TestDiffFiles/file2.txt';

        $speedyDiff = new SpeedyDiff($file1, $file2);

        $this->assertEquals($speedyDiff->getDiffOutput(), '2346' . PHP_EOL . 'asdf');
    }
}
