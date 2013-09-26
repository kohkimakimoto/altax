<?php
namespace Test\Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Kohkimakimoto\Altax\Application\AltaxApplication;
use Kohkimakimoto\Altax\Command\InitCommand;


class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new AltaxApplication();
        $command = $application->find('init');

        $path = tempnam(sys_get_temp_dir(), "InitCommandTest");
        unlink($path);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
          array('command' => $command->getName(), '--path' => $path));
        $this->assertEquals(true, is_file($path));

        $expectedContents = <<<EOL
<?php
/**
 * Altax Configurations.
 *
 * You need to modify this file for your environment.
 *
 * @see https://github.com/kohkimakimoto/altax
 * @author yourname <youremail@yourcompany.com>
 */

//
// Host and role configurations.
//
role('web', '127.0.0.1');

// or

// role('web', array('192.168.0.1', '192.168.0.2'));

// or

// host('192.168.0.1', 'web');
// host('192.168.0.2', 'web');

// or (Specify SSH Configurations) 

// host('192.168.0.2', array('port' => '22', 'login_name' => 'yourname', 'identity_file' => '/home/yourname/.ssh/id_rsa'), 'web');


//
// The Following is sample task definition.
//
desc('This is a sample task.');
task('sample',array('roles' => 'web'), function(\$host, \$args){

  run('echo Hellow World!');

});

EOL;

        $this->assertEquals($expectedContents, file_get_contents($path));

        unlink($path);
    }
}