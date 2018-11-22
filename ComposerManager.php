<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 19.11.2018
 * Time: 20:46
 */

class ComposerManager
{
    /**
     * @param array $packages
     */
    public function validatePackageDefinitions(array $packages):void
    {
        $this->comparingKeyAndValue($packages);
        $this->dependenciesExists($packages);
        $this->isDependenciesReal($packages);
        $this->noCycleDependencies($packages);
        $this->imitationOfDownloading();
    }

    /**
     * @param $packages
     */
    private function comparingKeyAndValue($packages):void
    {
        $Names = array_column($packages, 'name');
        $Keys = array_keys($packages);
        if ($Names !== $Keys) {
            throw new \http\Exception\InvalidArgumentException('package  not equal to it]\'s name');
        }
    }

    /**
     * @param $packages
     */
    private function dependenciesExists($packages):void
    {
        $dependencies = array_column($packages, 'dependencies');
        if ((array(count($packages))) !== (array(count($dependencies)))) {
            throw new \http\Exception\InvalidArgumentException('Not All Elements have dependencies');
        }

    }

    /**
     * @param $packages
     */
    private function isDependenciesReal($packages):void
    {
        $dependencies = array_column($packages, 'dependencies');
        $Keys = array_keys($packages);
        foreach ($dependencies as $dependency) {
            if (count(array_diff($dependency, $Keys)) !== 0) {
                throw new \http\Exception\InvalidArgumentException('Invalid dependency');
            }
        }
    }

    /**
     * @param $packages
     */
    private function noCycleDependencies($packages):void{

      foreach ($packages as  $package){
          $packageName=$package['name'];
          $dependency =$package['dependencies'];
          foreach ($dependency as $item){
              foreach ($packages as $pack){
                  if($pack['name']===$item && in_array($packageName, $pack['dependencies'])){
                      throw new \http\Exception\InvalidArgumentException('cycle dependencies');
                  }
              }
          }
      }
    }

    private function imitationOfDownloading():void{
        echo 'downloading packages... ';
        echo PHP_EOL;
        sleep(2);
        echo 'All packages has been downloaded successfully!';
        echo PHP_EOL;
    }

    /**
     * @param array $packages
     * @param string $packageName
     * @return array
     */
    private function getPackageDependencies(array $packages, string $packageName):array
    {
        static $dependencies = array();
        foreach ($packages[$packageName] as $package){
            if(is_array($package)){
                $dependencies =array_merge($dependencies,$package);
                foreach ($package as $item){
                    $this->getPackageDependencies($packages,$item);
                }
            }
        }
    return array_unique($dependencies);
    }

    /**
     * @param array $packages
     * @return array
     */
    private function checking(array $packages):array{
        $dependencies=[];
        foreach ($packages as $package){
            $x=count($package['dependencies']);
            $temp=['value'=>$x];
            $package=array_merge($package, $temp);
            $dependencies[]=$package;
        }
        usort($dependencies, $this->build_sorter('value') );
        return $dependencies;
    }

    /**
     * @param string $key
     * @return Closure
     */
    private function build_sorter(string $key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    /**
     * @param array $packagesSorted
     * @param array $dependencies
     * @return array
     */
    private function compare(array $packagesSorted, array $dependencies){
        $correctOrderDependencies=[];
        foreach ($packagesSorted as $item){
            if(in_array($item['name'],$dependencies)){
                $correctOrderDependencies[]=$item['name'];
            }
        }
        return $correctOrderDependencies;
    }

    /**
     * @param array $packages
     * @param string $packageName
     */
    public function getAllPackageDependencies(array $packages, string $packageName){
        $sorted =$this->checking($packages);
        $dependencies =$this->getPackageDependencies($packages, $packageName);
        $toView= $this->compare($sorted,$dependencies);
        foreach ($toView as $item){
            echo $item;
            echo ' ';
        }
    }
}

$packages = [
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
$composer = new ComposerManager();
$composer->validatePackageDefinitions($packages);
$composer->getAllPackageDependencies($packages, 'A');

?>