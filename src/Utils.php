<?php
namespace Nwidart\Modules;

use Exception;

class Utils
{
  /**
   * example usage
   * $appPath = getenv('APP_PATH');
   * $pathApp = $appPath.'bootstrap'.DIRECTORY_SEPARATOR.'app.php';
   * $identifier = modulesController
   * $moduleName = NameModule
   *
   * @param string $pathApp
   * @param string $identifier
   * @param string $moduleName
   * @return bool
   */
  public static function appendStringOnFile(string $pathApp = "", string $identifier = "", string $moduleName = "", string $content = "") : bool
    {
      try
      {
        if (empty($pathApp) || empty($identifier) || empty($moduleName)) {
          return false;
        }

        switch ($identifier) {

          case "modulesRoutes":
            $identifier = "//modulesRoutes";
            $lineString = $content;
          break;

          case "modulesBinds":
            $identifier = "//modulesBinds";
            $lineString = $content;
          break;

          case "Modelroutes":
            $identifier = "//routes";
            $lineString = $content;
          break;

          case "systemProviders":
            $identifier = "//systemProviders";
            $lineString = $content;
          break;

          default:
            return false;
        }

        $fileContent = file_get_contents($pathApp) or exit("Unable to open file!");

        if(!strpos($fileContent, $identifier)) {
            return false;
        }

        $file= fopen($pathApp, 'r+') or exit("Unable to open file!");

        $insertPos=0;
        $newline = "";
        if ($file) {
            while (!feof($file)) {
                $line = fgets($file);
                if (strpos($line, $identifier) !== false) {
                    $insertPos=ftell($file);
                    $newline =  $lineString;
                } else {
                    $newline .= $line;
                }
            }
            fseek($file, $insertPos);
            fwrite($file, $newline);
            fclose($file);
            return true;
        }
      } catch(Exception $ex) {
        return false;
      }
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    static function getModuleRegisterStatus(string $moduleName, string $registerName, $obj)
    {
        try {
            $file = str_replace("\\", "/",  base_path() . '/modules_registers.json');
            $content = json_decode(file_get_contents($file), true)[$registerName];

            foreach($content as $register => $value) {
                if($register === $moduleName && $value) {
                    $obj->warn("Register {$registerName} for {$moduleName} alredy exists");
                    return true;
                }
            }
        } catch(\Throwable $e) {
            $obj->error("Error on getting {$registerName} status in path : {$file}.");
        }

        return false;
    }

    /**
     * @param string $moduleName
     * @return void
     */
    static function saveModuleRegisterStatus(string $moduleName, string $registerName, $obj)
    {
        try {
            $file = str_replace("\\", "/",  base_path() . '/modules_registers.json');
            $content = json_decode(file_get_contents($file), true);
            $content[$registerName][$moduleName] = true;

            file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT));
            $obj->info("Success on registering {$registerName} status in path : {$file}");
        } catch(\Throwable $e) {
            $obj->error("Error on registering {$registerName} status in path : {$file}");
        }
    }
}
