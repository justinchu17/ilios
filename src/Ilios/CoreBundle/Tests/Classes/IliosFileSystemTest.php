<?php
namespace Ilios\CoreBundle\Tests\Classes;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use \Symfony\Component\HttpFoundation\File\File;

use Ilios\CoreBundle\Classes\IliosFileSystem;

class IliosFileSystemTest extends TestCase
{
    /**
     *
     * @var IliosFileSystem
     */
    private $iliosFileSystem;
    
    /**
     * Mock File System
     * @var FileSystem
     */
    private $mockFileSystem;
    
    /**
     * @var string
     */
    private $fakeTestFileDir;
    
    public function setUp()
    {
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }
        
        $this->mockFileSystem = m::mock('Symfony\Component\Filesystem\Filesystem');
        $this->mockFileSystem->shouldReceive('exists')->with($this->fakeTestFileDir)->andReturn(true);
        
        $this->iliosFileSystem = new IliosFileSystem($this->mockFileSystem, $this->fakeTestFileDir);
    }

    public function tearDown()
    {
        unset($this->mockFileSystem);
        unset($this->iliosFileSystem);
        unset($this->fakeTestFileDir);
        m::close();
    }

    public function testStoreLeaningMaterialFile()
    {
        $path = __FILE__;
        $newFilePath = $this->getTestFilePath($path);
        $file = m::mock('Symfony\Component\HttpFoundation\File\File')
            ->shouldReceive('getPathname')->andReturn($path)->getMock();
        $this->mockFileSystem->shouldReceive('copy')
            ->with($path, $newFilePath, false);
        $this->mockFileSystem->shouldReceive('mkdir');
        $this->iliosFileSystem->storeLearningMaterialFile($file);
    }

    public function testStoreLeaningMaterialFileReplaceFile()
    {
        $path = __FILE__;
        $newFilePath = $this->getTestFilePath($path);
        $file = m::mock('Symfony\Component\HttpFoundation\File\File')
            ->shouldReceive('getPathname')->andReturn($path)->getMock();
        $this->mockFileSystem->shouldReceive('exists')
            ->with($newFilePath)->andReturn(false);
        $this->mockFileSystem->shouldReceive('rename')
            ->with($path, $newFilePath);
        $this->mockFileSystem->shouldReceive('mkdir');
        $this->iliosFileSystem->storeLearningMaterialFile($file, false);
    }

    public function testStoreLeaningMaterialFileDontReplaceFileIfExists()
    {
        $path = __FILE__;
        $newFilePath = $this->getTestFilePath($path);
        $file = m::mock('Symfony\Component\HttpFoundation\File\File')
            ->shouldReceive('getPathname')->andReturn($path)->getMock();
        $this->mockFileSystem->shouldReceive('exists')
            ->with($newFilePath)->andReturn(true);
        $this->mockFileSystem->shouldReceive('mkdir');
        $this->iliosFileSystem->storeLearningMaterialFile($file, false);
    }

    public function testGetLearningMaterialFilePath()
    {
        $path = __FILE__;
        $file = m::mock('Symfony\Component\HttpFoundation\File\File')
            ->shouldReceive('getPathname')->andReturn($path)->getMock();
        $newPath = $this->iliosFileSystem->getLearningMaterialFilePath($file);
        $this->assertSame($this->fakeTestFileDir . '/' . $newPath, $this->getTestFilePath($path));
    }

    public function testRemoveFile()
    {
        $file = 'foojunk';
        $this->mockFileSystem->shouldReceive('remove')->with($this->fakeTestFileDir . '/' . $file);
        $this->iliosFileSystem->removeFile($file);
    }

    public function testGetFile()
    {
        $fs = new SymfonyFileSystem();
        $someJunk = 'whatever dude';
        $hash = md5($someJunk);
        $hashDirectory = substr($hash, 0, 2);
        $parts = [
            $this->fakeTestFileDir,
            'learning_materials',
            'lm',
            $hashDirectory
        ];
        $dir = implode($parts, '/');
        $fs->mkdir($dir);
        $testFilePath = $dir . '/' . $hash;
        file_put_contents($testFilePath, $someJunk);
        $file = m::mock('Symfony\Component\HttpFoundation\File\File')
            ->shouldReceive('getPathname')->andReturn($testFilePath)->getMock();
        $this->mockFileSystem->shouldReceive('exists')
            ->with($testFilePath)->andReturn(true);
        $this->mockFileSystem->shouldReceive('mkdir');
        $newPath = $this->iliosFileSystem->storeLearningMaterialFile($file, false);
        
        $newFile = $this->iliosFileSystem->getFile($newPath);
        $this->assertSame($newFile->getPathname(), $testFilePath);
        $this->assertSame(file_get_contents($newFile->getPathname()), $someJunk);
        $fs->remove($this->fakeTestFileDir . '/learning_materials');
    }

    public function testCheckLearningMaterialFilePath()
    {
        $goodLm = m::mock('Ilios\CoreBundle\Entity\LearningMaterial')
            ->shouldReceive('getRelativePath')->andReturn('goodfile')
            ->mock();
        $badLm = m::mock('Ilios\CoreBundle\Entity\LearningMaterial')
            ->shouldReceive('getRelativePath')->andReturn('badfile')
            ->mock();
        $this->mockFileSystem->shouldReceive('exists')
            ->with($this->fakeTestFileDir . '/goodfile')->andReturn(true)->once();
        $this->mockFileSystem->shouldReceive('exists')
            ->with($this->fakeTestFileDir . '/badfile')->andReturn(false)->once();
        $this->assertTrue($this->iliosFileSystem->checkLearningMaterialFilePath($goodLm));
        $this->assertFalse($this->iliosFileSystem->checkLearningMaterialFilePath($badLm));
    }
    
    
    protected function getTestFilePath($path)
    {
        $hash = md5_file($path);
        $hashDirectory = substr($hash, 0, 2);
        $parts = [
            $this->fakeTestFileDir,
            'learning_materials',
            'lm',
            $hashDirectory,
            $hash
        ];
        return implode($parts, '/');
    }

    public function testGetSymfonyFileForPath()
    {
        $fs = new SymfonyFileSystem();
        $someJunk = 'whatever dude';
        $hash = md5($someJunk);
        $hashDirectory = substr($hash, 0, 2);
        $parts = [
            $this->fakeTestFileDir,
            'learning_materials',
            'lm',
            $hashDirectory
        ];
        $dir = implode($parts, '/');
        $fs->mkdir($dir);
        $testFilePath = $dir . '/' . $hash;
        file_put_contents($testFilePath, $someJunk);
        $file = $this->iliosFileSystem->getSymfonyFileForPath($testFilePath);
        
        $this->assertTrue($file instanceof File);
        $this->assertSame($testFilePath, $file->getPathname());
        $this->assertSame(file_get_contents($file->getPathname()), $someJunk);

        $fs->remove($this->fakeTestFileDir . '/learning_materials');
    }
}
