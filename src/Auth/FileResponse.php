<?php
namespace App\Auth;
use Slim\Http\Response;
use Slim\Psr7\Stream;
/**
 * Class FileResponse
 * @package mhndev\slimFileResponse
 */
class FileResponse
{
    /**
     * @param Response $response
     * @param string $fileName
     *
     * @param null $outputName
     * @return Response|static
     */
    public static function getResponse(Response $response, $fileName, $outputName = null)
    {
        if ($fd = fopen ($fileName, "r")) {
            $size = filesize($fileName);
            $path_parts = pathinfo($fileName);
            $ext = strtolower($path_parts["extension"]);
            if(!$outputName) {
                $outputName = $path_parts["basename"];
            }else{
                if(count(explode('.', $outputName)) <= 1){
                    $outputName = $outputName.'.'.$ext;
                }
            }
            switch ($ext) {
               
                case "png":
                    $response = $response->withHeader("Content-type","image/png");
                    break;
                case "gif":
                    $response = $response->withHeader("Content-type","image/gif");
                    break;
                case "jpeg":
                    $response = $response->withHeader("Content-type","image/jpeg");
                    break;
                case "jpg":
                    $response = $response->withHeader("Content-type","image/jpg");
                    break;
                default;
                    $response = $response->withHeader("Content-type","application/octet-stream");
                    break;
            }
            $response = $response->withHeader("Content-Disposition",'filename="'.$outputName.'"');
            $response = $response->withHeader("Cache-control","private");
            $response = $response->withHeader("Content-length",$size);
            ob_clean();
        }
        $stream = new Stream($fd);
        $response = $response->withBody($stream);
        return $response;
    }
}