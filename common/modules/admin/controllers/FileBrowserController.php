<?php

namespace common\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use common\modules\admin\rbac\Permission;
use common\modules\admin\Module;


class FileBrowserController extends \yii\web\Controller
{
    private $serverUploadDir = ".";
    private $defaultUploadDir = "/uploads/";
    private $imageSubDir = "images";
    private $fileSubDir = "files";
    private $imageAllowedExtensions = array("bmp", "gif", "jpeg", "jpg", "png");
    private $fileAllowedExtensions = array("7z", "aiff", "asf", "avi", "bmp", "csv", "doc", "fla", "flv", "gif", "gz", "gzip", "jpeg", "jpg", "mid", "mov", "mp3", "mp4", "mpc", "mpeg", "mpg", "ods", "odt", "pdf", "png", "ppt", "pxd", "qt", "ram", "rar", "rm", "rmi", "rmvb", "rtf", "sdc", "sitd", "swf", "sxc", "sxw", "tar", "tgz", "tif", "tiff", "txt", "vsd", "wav", "wma", "wmv", "xls", "xml", "zip");

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Permission::UPLOAD],
                        'roleParams' => ['site_id' => Yii::$app->request->get('site_id')],
                    ],
                ],
            ],
        ];
    }

    public function actionBrowser($type, $CKEditorFuncNum, $CKEditor, $langCode)
    {
        $uploadDir = $this->getUploadDir($type);
        $fileBrowserList = $this->getFileBrowserList($uploadDir);
        return $this->renderPartial('browser', ['uploadDir' => $uploadDir, 'fileBrowserList' => $fileBrowserList]);
    }

    public function actionBrowserItems()
    {
        $directory = Yii::$app->request->post('directory');
        $fileBrowserList = $this->getFileBrowserList($directory);
        return $this->renderPartial('browser-items', ['fileBrowserList' => $fileBrowserList]);
    }

    public function actionUploadFile()
    {
        $request = Yii::$app->request;
        try {
            $directory = $request->post('directory');
            $fileName = basename($_FILES["upload"]["name"]);
            $type = $this->getTypeFile($fileName);
            if (!$this->fileExtensionAllowed($fileName, $type)) {
                throw new \InvalidArgumentException(Module::t('error', 'The file extension does not allow'));
            } else {
                $pos = strpos($directory, $this->defaultUploadDir);
                if ($pos === false || $pos !== 0) {
                    throw new \InvalidArgumentException('directory');
                }

                $target_file = $this->serverUploadDir . $directory . $fileName;
                if (file_exists($target_file)) {
                    throw new \InvalidArgumentException(Module::t('error', 'The file already exists'));
                }

                if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
                    return $directory . $fileName;
                } else {
                    throw new \InvalidArgumentException(Module::t('error', 'The file name is not valid'));
                }
            }
        } catch (\Exception $ex) {
            Yii::$app->response->statusCode = 500;
            return $ex->getMessage();
        }
    }

    public function actionCreateDirectory()
    {
        try {
            $request = Yii::$app->request;
            $name = $request->post('name');
            $directory = $request->post('directory');

            if (empty($name)) {
                throw new \InvalidArgumentException('name');
            }

            $pos = strpos($directory, $this->defaultUploadDir);
            if ($pos === false || $pos !== 0) {
                throw new \InvalidArgumentException('directory');
            }

            $newDir = $directory . $name . "/";
            $dir = $this->serverUploadDir . $newDir;
            if (mkdir($dir)) {
                $fileBrowserList = $this->getFileBrowserList($newDir);
                return $this->renderPartial('browser-items', ['fileBrowserList' => $fileBrowserList]);
            } else {
                throw new \InvalidArgumentException('directory');
            }
        } catch (\Exception $ex) {
            Yii::$app->response->statusCode = 500;
            return $ex->getMessage();
        }
    }

    private function getFileBrowserList($directory)
    {
        $fileBrowserList = array();
        $dir = $this->serverUploadDir . $directory;
        $dirItems = scandir($dir);
        foreach ($dirItems as $item) {
            if (!in_array($item, array(".", ".."))) {
                if (is_dir($dir . $item)) {
                    $fileBrowserList[] = array("name" => $item, "url" => $directory . $item . "/", "type" => "directory");
                } else {
                    $fileBrowserList[] = array("name" => $item, "url" => $directory . $item, "type" => $this->getTypeFile($item));
                }
            }
        }
        return $fileBrowserList;
    }

    private function getUploadDir($type)
    {
        if ($type == "image") {
            return $this->defaultUploadDir . $this->imageSubDir . "/";
        } else
            if ($type == "file") {
                return $this->defaultUploadDir . $this->fileSubDir . "/";
            }
        return $this->defaultUploadDir;
    }

    private function getTypeFile($fileName)
    {
        if (in_array($this->getExtension($fileName), $this->imageAllowedExtensions)) {
            return "image";
        }
        return "file";
    }

    private function fileExtensionAllowed($fileName, $type)
    {
        if ($type == "image") {
            return in_array($this->getExtension($fileName), $this->imageAllowedExtensions);
        } else {
            return in_array($this->getExtension($fileName), $this->fileAllowedExtensions);
        }
    }

    private function getExtension($fileName)
    {
        $info = new \SplFileInfo($fileName);
        return strtolower($info->getExtension());
    }
}
