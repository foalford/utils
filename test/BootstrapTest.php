<?php

namespace UtilsTest;

use PHPUnit_Framework_TestCase as TestCase;
use function Foalford\Utils\Bootstrap;

class BootstrapTest extends TestCase
{
    protected $tmpdir;

    public function setUp()
    {
        $this->tmpdir = 'test-'.md5(random_bytes(5));
        if (mkdir($this->tmpdir)) {
            chdir($this->tmpdir);
            file_put_contents('global.config.php', <<<EOT
<?php return ['service_key' => [ 'setting' => 'global', 'key2' => 'global' ]];
EOT
            );
            file_put_contents('local.config.php', <<<EOT
<?php return ['service_key' => [ 'setting' => 'local' ]];
EOT
            );
        } else {
            throw new \Exception('Cannot make fixture dir');
        }
    }

    public function tearDown()
    {
		chdir('..');
        if (is_dir($this->tmpdir)) {
            $this->delTree($this->tmpdir);
        }
    }

    public function testDeepMerge()
    {
        $locator = bootstrap('{global,local}.config.php');
        $config = $locator('config');
        $this->assertTrue(is_array($config['service_key']));
        $this->assertArrayHasKey('setting', $config['service_key']);
        $this->assertArrayHasKey('key2', $config['service_key']);
        $this->assertEquals('local', $config['service_key']['setting']);
    }

    protected function delTree($dir) 
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
          (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    } 
}
