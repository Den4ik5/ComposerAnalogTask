<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 19.11.2018
 * Time: 20:46
 */

class ComposerManager
{
    private $packages = [
        'A' => [
            'name' => 'A',
            'dependencies' => ['B', 'C'],
        ],
        'B' => [
            'name' => 'B',
            'dependencies' => [],
        ],
        'C' => [
            'name' => 'C',
            'dependencies' => ['B', 'D'],
        ],
        'D' => [
            'name' => 'D',
            'dependencies' => [],
        ],
        'E' => [
            'name' => 'E',
            'dependencies' => ['A'],
        ],
    ];

    /**
     * @return void
     */
    public function validatePackageDefinitions()
    {
        $this->comparingKeyAndValue();
        $this->dependenciesExists();
        $this->isDependenciesReal();
        $this->noCycleDependencies();
        $this->imitationOfDownloading();
    }

    /**
     * @return void
     */
    private function comparingKeyAndValue():void
    {
        $packages = $this->packages;
        $Names = array_column($packages, 'name');
        $Keys = array_keys($packages);
        if ($Names !== $Keys) {
            throw new \http\Exception\InvalidArgumentException('package  not equal to it]\'s name');
        }
    }

    /**
     * @return void
     */
    private function dependenciesExists():void
    {
        $packages = $this->packages;
        $dependencies = array_column($packages, 'dependencies');
        if ((array(count($packages))) !== (array(count($dependencies)))) {
            throw new \http\Exception\InvalidArgumentException('Not All Elements have dependencies');
        }

    }

    /**
     * @return void
     */
    private function isDependenciesReal():void
    {
        $packages = $this->packages;
        $dependencies = array_column($packages, 'dependencies');
        $Keys = array_keys($packages);
        foreach ($dependencies as $dependency) {
            if (count(array_diff($dependency, $Keys)) !== 0) {
                throw new \http\Exception\InvalidArgumentException('Invalid dependency');
            }
        }
    }

    /**
     * @return void
     */
    private function noCycleDependencies():void{

      foreach ($this->packages as  $package){
          $packageName=$package['name'];
          $dependency =$package['dependencies'];
          foreach ($dependency as $item){
              foreach ($this->packages as $pack){
                  if($pack['name']===$item && in_array($packageName, $pack['dependencies'])){
                      throw new \http\Exception\InvalidArgumentException('cycle dependencies');
                  }
              }
          }
      }
    }
    /**
     * @return void
     */
    private function imitationOfDownloading(){
        echo 'downloading packages... ';
        echo PHP_EOL;
        sleep(2);
        echo 'All packages has been downloaded successfully!';
        echo PHP_EOL;
    }

    /**
     * @param string $packageName
     * @return array
     */
    public function getAllPackageDependencies(string $packageName):array
    {
        $dependencies=array();
        foreach ($this->packages as $item){
            if ($item['name']==='$packageName'){
                echo $packageName['name'];
               $dependencies=$this->getDependency($packageName,$dependencies);
            }
        }
        return $dependencies;
    }

    /**
     * @param String $packageName
     * @param array $dependencies
     * @return mixed
     */
    private function getDependency(String $packageName, array $dependencies)
    {
        if (!in_array($packageName,$dependencies)){
            array_push($dependencies, $packageName);
            foreach ($this->packages[$packageName]['dependencies'] as $item) {
                if (!in_array($item,$dependencies)){
                    return $this->getDependency($item, $dependencies);
                }
            }
        }
    }
}
$composer = new ComposerManager();
$dependencies = array();

$composer->validatePackageDefinitions();
$composer-> getAllPackageDependencies('B');

?>