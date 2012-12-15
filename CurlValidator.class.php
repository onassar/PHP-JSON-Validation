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
            // "head" for content type
            $curler = RequestCache::read('curlers', $url, 'head');
            if ($curler === null) {
                $curler = (new Curler());
                RequestCache::write('curlers', $url, 'head', $curler);
                $curler->head($url);
            }
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
            // "get" for content type
            $curler = RequestCache::read('curlers', $url, 'get');
            if ($curler === null) {
                $curler = (new Curler());
                RequestCache::write('curlers', $url, 'get', $curler);
                $curler->get($url);
            }
            $charset = $curler->getCharset();
            return $charset !== false;
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
            // "get" for content
            $curler = RequestCache::read('curlers', $url, 'get');
            if ($curler === null) {
                $curler = (new Curler());
                RequestCache::write('curlers', $url, 'get', $curler);
                $curler->get($url);
            }
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
            // "get" for content; check info for download size
            $curler = RequestCache::read('curlers', $url, 'get');
            if ($curler === null) {
                $curler = (new Curler());
                RequestCache::write('curlers', $url, 'get', $curler);
                $curler->get($url);
            }
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
            // "head" for content type
            $curler = RequestCache::read('curlers', $url, 'head');
            if ($curler === null) {
                $curler = (new Curler());
                RequestCache::write('curlers', $url, 'head', $curler);
                $curler->head($url);
            }
            $info = $curler->getInfo();
            return isset($info['content_type'])
                && strstr($info['content_type'], 'text/html') !== false;
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
        public static function urlStatusCode($url, $allowedStatusCodes = array(200))
        {
            // "head" for content type
            $curler = RequestCache::read('curlers', $url, 'head');
            if ($curler === null) {
                $curler = (new Curler());
                RequestCache::write('curlers', $url, 'head', $curler);
                $curler->head($url);
            }
            $info = $curler->getInfo();
            return isset($info['http_code'])
                && in_array($info['http_code'], $allowedStatusCodes);
        }
    }
