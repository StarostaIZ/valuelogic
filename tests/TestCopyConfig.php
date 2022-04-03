<?php


use PHPUnit\Framework\TestCase;

class TestCopyConfig extends TestCase
{

    public function testCommandWithLessArguments()
    {
        exec("php ../copy_config.php base_config.json", $output, $resultCode);
        $this->assertEquals(0, $resultCode);
        $this->assertEquals("You should pass exactly 2 files!", $output[0]);
    }

    public function testCommandWhenFileNotExist()
    {
        exec("php ../copy_config.php base_config.json config_not_exists.json", $output, $resultCode);
        $this->assertEquals(0, $resultCode);
        $this->assertEquals("Invalid file paths!", $output[0]);
    }

    public function testCommandWithInvalidParameterKey()
    {
        exec("php ../copy_config.php " . __DIR__ . "/data/base_config.json " . __DIR__ . "/data/invalid_parameters.json", $output, $resultCode);
        $this->assertEquals(0, $resultCode);
        $this->assertEquals("Invalid config file data, key not exist!", $output[0]);
    }

    public function testCommandWithInvalidParameterNotArray()
    {
        exec("php ../copy_config.php " . __DIR__ . "/data/base_config.json " . __DIR__ . "/data/invalid_parameters_array.json", $output, $resultCode);
        $this->assertEquals(0, $resultCode);
        $this->assertEquals("Invalid config file data, properties should be an array!", $output[0]);
    }

    public function testCommandWithValidParameters()
    {
        exec("php ../copy_config.php " . __DIR__ . "/data/base_config.json " . __DIR__ . "/data/parameters_1.json test_output", $output, $resultCode);
        $this->assertEquals(0, $resultCode);
        $fi = new FilesystemIterator(__DIR__ . "/test_output/", FilesystemIterator::SKIP_DOTS);
        $this->assertCount(3, $fi);

        $std = json_decode(file_get_contents($fi->getPathname()));
        $this->assertEquals(6, $std->details->buffer_size);
        $fi->next();

        $std = json_decode(file_get_contents($fi->getPathname()));
        $this->assertEquals(2, $std->details->buffer_size);
        $fi->next();

        $std = json_decode(file_get_contents($fi->getPathname()));
        $this->assertEquals(3, $std->details->buffer_size);

    }


    public static function tearDownAfterClass(): void
    {
        if (!is_dir(__DIR__ . '/test_output')) {
            return;
        }
        array_map('unlink', array_filter((array)glob(__DIR__ . '/test_output/*')));
        rmdir(__DIR__ . '/test_output');
    }
}
