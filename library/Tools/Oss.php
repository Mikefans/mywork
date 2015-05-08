<?php
namespace Hlg;

use Exception;

/**
 * 对 ALIOSS的封装
 * 调用示例 $oss = new \Hlg\Oss(); $oss->load(..);
 * 有错误将记录日志后，异常抛出
 */
class Oss
{

    private $_retryCount = 3;

    protected $_alioss;

    private $_bucket = 'hlg_icon';
    
    const ALIYUN_OSS_DOMAIN = "http://oss.aliyuncs.com/";

    public function __construct($bucket = '')
    {
        include_once APP_PATH . DS . 'library' . DS . 'Aliyun' . DS . 'Oss' . DS . "sdk.class.php";
        $this->_alioss = new \ALIOSS();
        if ($bucket) {
            $this->_bucket = $bucket;
        }
    }

    public function setIconBucket()
    {
        $this->_bucket = 'hlg_icon';
        return $this;
    }

    public function setBucket($bucket)
    {
        $this->_bucket = $bucket;
        return $this;
    }

    public function getBucket()
    {
        return $this->_bucket;
    }

    /**
     * 从OSS下载一个文件到本地
     *
     * @param unknown_type $fileId            
     * @param string $localFile
     *            若为空，则直接返回内容
     */
    public function load($fullPath, $localFile = '')
    {
        list ($bucket, $object) = explode('/', $fullPath, 2);
        $allContent = '';
        $totalSize = 0;
        $offset = 0;
        $persize = 1024 * 1024; // 次load 4096字节
        $times = 1;
        try {
            
            $bytes = 'bytes=' . $persize * $offset . '-' . ($persize * ($offset + 1) - 1);
            $options[\ALIOSS::OSS_HEADERS] = array(
                'range' => $bytes
            );
            $result = $this->_sendOssRequest('get_object', array(
                $bucket,
                $object,
                $options
            ));
            $allContent .= $result->body;
            
            // 获取总的字节数
            $header = $result->header;
            if (isset($header['content-range'])) {
                $totalSize = (int) substr($header['content-range'], 1 + strpos($header['content-range'], '/'));
                $times = ceil($totalSize / $persize);
            }
            
            for ($i = 1; $i < $times; $i ++) {
                // $bytes = 'bytes='.$persize*$i.'-'.($persize*($i+1)-1);
                if ($i == $times - 1) {
                    $bytes = 'bytes=' . $persize * $i . '-' . ($totalSize - 1);
                } else {
                    $bytes = 'bytes=' . $persize * $i . '-' . ($persize * ($i + 1) - 1);
                }
                $options[\ALIOSS::OSS_HEADERS] = array(
                    'range' => $bytes
                );
                $result = $this->_sendOssRequest('get_object', array(
                    $bucket,
                    $object,
                    $options
                ));
                $allContent .= $result->body;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        if ($localFile) {
            if (APPHOST == 'ACE') {
                \Hlg::setError('ACE不能写文件到本地');
                return false;
            }
            $fp = fopen($localFile, 'wb');
            fwrite($fp, $allContent);
            fclose($fp);
            return true;
        } else {
            return $allContent;
        }
    }

    /**
     * 上传文件到OSS
     *
     * @param String|binary $localFile            
     * @param String $suffix            
     * @param bool $content
     *            true:文件内容 false：本地文件名
     * @return String 包含bucket的文件路径,如:hlg_mybucket0001/144778_1324889020.0078.jpg
     */
    public function upload($bucket, $shopId, $localFile, $suffix, $isContent = false)
    {
        // $bucket = $this->getBucket();
        $object = date('Ymd') . '/' . $shopId . '_' . date('His') . '_' . rand(0, 10000) . '.' . $suffix;
        if ($isContent) {
            $content = $localFile;
            $length = strlen($content); // 获取长度
        } else {
            $content = '';
            $length = 0;
            $fp = fopen($localFile, 'rb');
            if ($fp) {
                $f = fstat($fp);
                $length = $f['size'];
                while (! feof($fp)) {
                    $content .= fgets($fp, 8192);
                }
            }
            fclose($fp);
        }
        // add by gaolou 当前处理用的编码是UTF-8，所以如果需要上传为GBK文件，需要将内容转为GBK上传， 上传后文件的编码是由内容决定的
        // $content = iconv("UTF-8", "GBK", $content);
        
        $upload_file_options = array(
            'content' => $content,
            'length' => $length
        );
        
        try {
            $result = $this->_sendOssRequest('upload_file_by_content', array(
                $bucket,
                $object,
                $upload_file_options
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        $objectUrl = $bucket . '/' . $object;
        // $objectUrl = $this->_alioss->get_object_url($bucket, $object);
        return $objectUrl;
    }

    /**
     * 删除文件
     *
     * @param 文件名或空目录 $object
     *            空目录写法 path1/
     */
    public function delete($fullPath)
    {
        list ($bucket, $object) = explode('/', $fullPath, 2);
        try {
            $this->_sendOssRequest('delete_object', array(
                $bucket,
                $object
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function getBucketAcl($bucket)
    {
        $option = array(
            'Content-Type' => 'text/xml'
        );
        try {
            $this->_sendOssRequest('get_bucket_acl', array(
                $bucket,
                $option
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    /**
     * 设置指定bucket的ACL,目前只有三种acl private,public-read,public-read-write
     *
     * @param unknown_type $acl
     *            取值只能是private,public-read,public-read-write其中之一
     * @return unknown
     */
    public function setBucketAcl($bucket, $acl)
    {
        try {
            $this->_sendOssRequest('set_bucket_acl', array(
                $bucket,
                $acl
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function createBucket($bucket, $acl = 'private')
    {
        try {
            $this->_sendOssRequest('create_bucket', array(
                $bucket,
                $acl
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function deleteBucket($bucket)
    {
        try {
            $this->_sendOssRequest('delete_bucket', array(
                $bucket
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function listObject($bucket)
    {
        $options = array(
            'delimiter' => '/',
            'prefix' => '',
            'max-keys' => 10,
            'marker' => 'fff/'
        );
        try {
            $this->_sendOssRequest('list_object', array(
                $bucket,
                $options
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function createObjectDir($bucket, $object)
    {
        try {
            $this->_sendOssRequest('create_object_dir', array(
                $bucket,
                $object
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function isObjectExist($bucket, $object)
    {
        try {
            $this->_sendOssRequest('is_object_exist', array(
                $bucket,
                $object
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function getObjectUrl($bucket, $object)
    {
        try {
            $this->_sendOssRequest('get_object_url', array(
                $bucket,
                $object
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function getObjectMeta($bucket, $object)
    {
        try {
            $this->_sendOssRequest('get_object_meta', array(
                $bucket,
                $object
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function createObjectGroup($bucket, $object_group, $object_group_array)
    {
        // $object_group_array = array(
        // 'object0003/4.txt',
        // 'object0003',
        // );
        try {
            $this->_sendOssRequest('create_object_group', array(
                $bucket,
                $object_group,
                $object_group_array
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function getObjectGroupIndex($bucket, $objectGroup)
    {
        try {
            $this->_sendOssRequest('get_object_group_index', array(
                $bucket,
                $objectGroup
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function getObjectGroupMeta($bucket, $objectGroup)
    {
        try {
            $this->_sendOssRequest('get_object_group_meta', array(
                $bucket,
                $objectGroup
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function deleteObjectGroup($bucket, $objectGroup)
    {
        try {
            $this->_sendOssRequest('delete_object_group', array(
                $bucket,
                $objectGroup
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    /**
     * 发送请求到OSS，支持重试
     *
     * @param 需要调用的方法名 $method            
     * @param 参数 $params            
     * @return RequestCore对象
     */
    private function _sendOssRequest($method, $params = array())
    {
        $retry = $this->_retryCount;
        $stopRetry = false;
        while ($retry > 0 && ! $stopRetry) {
            $retry --;
            try {
                $response = call_user_func_array(array(
                    $this->_alioss,
                    $method
                ), $params);
            } catch (\Exception $e) {
                if ($retry <= 0) {
                    $stopRetry = true;
                    \Hlg::logException($e, 'ossApiError.log');
                    throw new Exception($e->getMessage());
                } else {
                    continue;
                }
            }
            if (! $response->isOK()) {
                if (! isset($response->status) || ! isset($response->body)) {
                    \Hlg::logException('Alioss返回数据格式(无STATUS或BODY块)不正确:' . var_export($response, true), 'ossApiError.log');
                    \Hlg::setError('Alioss返回数据格式不正确');
                }
                $statusCode = $response->status;
                $errorObj = @simplexml_load_string($response->body);
                if ($errorObj === false) {
                    \Hlg::logException('Alioss返回数据格式(非XML格式)不正确:' . var_export($response, true), 'ossApiError.log');
                    \Hlg::setError('Alioss返回数据格式不正确');
                }
                $errorCode = trim($errorObj->Code);
                $errorMessage = trim($errorObj->Message);
                if ((400 == $statusCode && 'RequestTimeout' == $errorCode) || (500 == $statusCode && 'InternalError' == $errorCode)) {
                    if ($retry > 0) {
                        continue;
                    }
                    \Hlg::logException('Response:' . $response->body, 'ossApiError.log');
                    \Hlg::setError('调用OSS系统错误:' . $errorMessage);
                } else {
                    \Hlg::logException('Response:' . $response->body, 'ossApiError.log');
                    \Hlg::setError('调用OSS系统错误:' . $errorMessage);
                }
            }
            $stopRetry = true;
        }
        
        return $response;
    }
}
