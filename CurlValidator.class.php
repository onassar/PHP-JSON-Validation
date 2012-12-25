<?php

    // dependency check
    if (class_exists('Curler') === false) {
        throw new Exception(
            '*Curler* class required. Please see ' .
            'https://github.com/onassar/PHP-Curler'
        );
    }

    // dependency check
    if (class_exists('RequestCache') === false) {
        throw new Exception(
            '*RequestCache* class required. Please see ' .
            'https://github.com/onassar/PHP-RequestCache'
        );
    }

    /**
     * CurlValidator
     * 
     * @author   Oliver Nassar <onassar@gmail.com>
     * @abstract
     */
    abstract class CurlValidator
    {
        /**
         * _getCurler
         * 
         * @note   Amazon doesn't seem to support head requests (anymore). To
         *         get around this, I attempt a head, when required, and if a
         *         405 status code is found ("method not supported"), I digrade
         *         to a get
         * @access public
         * @static
         * @param  string $url
         * @param  string $method
         * @return Curler
         */
        protected static function _getCurler($url, $method)
        {
            $curler = RequestCache::read('curlers', $url, $method);
            if ($curler === null) {
                $curler = (new Curler());
                RequestCache::write('curlers', $url, $method, $curler);
                call_user_func(array($curler, $method), $url);

                if ($method === 'head') {
                    $info = $curler->getInfo();
                    $statusCode = (int) $info['http_code'];

                    if ($statusCode === 405) {
                        RequestCache::write('curlers', $url, $method, $curler);
                        call_user_func(array($curler, 'get'), $url);
                    }
                }

            }
            return $curler;
        }

        /**
         * numberOfRedirectsIsLessThan
         * 
         * @access public
         * @static
         * @param  String $url
         * @param  Integer $redirectLimit
         * @return Boolean
         */
        public static function numberOfRedirectsIsLessThan($url, $redirectLimit)
        {
            $curler = self::_getCurler($url, 'head');
            $info = $curler->getInfo();
            return isset($info['redirect_count'])
                && (int) $info['redirect_count'] < $redirectLimit;
        }

        /**
         * urlCharsetDefined
         * 
         * @access public
         * @static
         * @param  String $url
         * @return Boolean
         */
        public static function urlCharsetDefined($url)
        {
            $curler = self::_getCurler($url, 'get');
            $charset = $curler->getCharset();
            return $charset !== false;
        }

        /**
         * urlCharsetSupported
         * 
         * @access public
         * @static
         * @param  String $url
         * @return Boolean
         */
        public static function urlCharsetSupported($url)
        {
            $curler = self::_getCurler($url, 'get');
            $charset = $curler->getCharset();
            return StringValidator::inList(
                $charset,
                array(
                    'utf-8',
                    'ascii',
                    'iso-8859-1',
                    'euc-kr',
                    'euc-jp'
                )
            );
        }

        /**
         * urlContentIsNotEmpty
         * 
         * @access public
         * @static
         * @param  String $url
         * @return Boolean
         */
        public static function urlContentIsNotEmpty($url)
        {
            $curler = self::_getCurler($url, 'get');
            $response = $curler->getResponse();
            return !empty($response);
        }

        /**
         * urlContentSizeIsLessThan
         * 
         * @access public
         * @static
         * @param  String $url
         * @param  Integer $maxKilobytes
         * @return Boolean
         */
        public static function urlContentSizeIsLessThan($url, $maxKilobytes)
        {
            $curler = self::_getCurler($url, 'get');
            $info = $curler->getInfo();
            $contentSizeInBytes = (int) $info['size_download'];
            return $contentSizeInBytes > 0
                && $contentSizeInBytes < $maxKilobytes * 1024;
        }

        /**
         * urlContentTypeIsHtml
         * 
         * @access public
         * @static
         * @param  String $url
         * @return Boolean
         */
        public static function urlContentTypeIsHtml($url)
        {
            $curler = self::_getCurler($url, 'head');
            $info = $curler->getInfo();
            return isset($info['content_type'])
                && strstr(strtolower($info['content_type']), 'text/html') !== false;
        }

        /**
         * urlStatusCode
         * 
         * @access public
         * @static
         * @param  String $url
         * @param  Array $allowedStatusCodes (default: array(200))
         * @return Boolean
         */
        public static function urlStatusCode(
            $url, array $allowedStatusCodes = array(200)
        ) {
            $curler = self::_getCurler($url, 'head');
            $info = $curler->getInfo();
            return isset($info['http_code'])
                && in_array($info['http_code'], $allowedStatusCodes);
        }
    }
