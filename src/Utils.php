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

          default:
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
}
