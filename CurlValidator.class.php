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
         * _getHeadInfo
         * 
         * @note   Amazon doesn't seem to support head requests (anymore). To
         *         get around this, I attempt a head, when required, and if a
         *         405 status code is found ("method not supported"), I digrade
         *         to a get
         * @note   Amazon gave 405 upon HEAD
         * @note   CTVNews gave 503 upon HEAD
         * @access public
         * @static
         * @param  string $url
         * @return array
         */
        protected static function _getHeadInfo($url)
        {
            $curler = RequestCache::read('curler');
            $headInfo = $curler->getHeadInfo();
            if (is_null($headInfo)) {
                $headInfo = $curler->head($url);
            }
            $statusCode = (int) $headInfo['http_code'];

            /**
             * Originally, it was only checking for a non-200 status code.
             * However if the url returns a 404 status code, the `get` call
             * below will return false.
             * 
             * This is because by default, `Curler` instances see a 404 as a
             * failed request, unless otherwise specified during instantiation.
             * 
             * Accompanying this false value will be a null response from the
             * `getInfo` call below. This being null will result in failing
             * rules below.
             */
            if (
                $statusCode !== 200
                && $statusCode !== 404
            ) {
                $urlBody = $curler->getResponse();
                if (is_null($urlBody)) {
                    $urlBody = $curler->get($url);
                }
                return $curler->getInfo();
            }
            return $headInfo;
        }

        /**
         * numberOfRedirectsIsLessThan
         * 
         * @access public
         * @static
         * @param  string $url
         * @param  integer $redirectLimit
         * @return boolean
         */
        public static function numberOfRedirectsIsLessThan($url, $redirectLimit)
        {
            $headInfo = self::_getHeadInfo($url);
            return isset($headInfo['redirect_count'])
                && (int) $headInfo['redirect_count'] < $redirectLimit;
        }

        /**
         * urlContainsHeadTag
         * 
         * @access public
         * @static
         * @param  string $url
         * @return boolean
         */
        public static function urlContainsHeadTag($url)
        {
            $curler = RequestCache::read('curler');
            $urlBody = $curler->getResponse();
            if (is_null($urlBody)) {
                $urlBody = $curler->get($url);
            }
            return strstr($urlBody, '<head') !== false;
        }

        /**
         * urlCharsetDefined
         * 
         * @access public
         * @static
         * @param  string $url
         * @return boolean
         */
        public static function urlCharsetDefined($url)
        {
            $curler = RequestCache::read('curler');
            $urlBody = $curler->getResponse();
            if (is_null($urlBody)) {
                $urlBody = $curler->get($url);
            }
            $charset = $curler->getCharset();
            return $charset !== false;
        }

        /**
         * urlCharsetSupported
         * 
         * @access public
         * @static
         * @param  string $url
         * @return boolean
         */
        public static function urlCharsetSupported($url)
        {
            $curler = RequestCache::read('curler');
            $urlBody = $curler->getResponse();
            if (is_null($urlBody)) {
                $urlBody = $curler->get($url);
            }
            $charset = $curler->getCharset();
            $urlEncodedInSupportCharset = StringValidator::inList(
                $charset,
                array(
                    'utf-8',
                    'ascii',
                    'iso-8859-1',
                    'euc-kr',
                    'euc-jp',
                    'windows-1252'
                )
            );
            if ($urlEncodedInSupportCharset === false && !defined('CRON')) {
                sendLoggingEmail(
                    '[' . getHost() . '] CurlValidator::urlCharsetSupport',
                    nl2br(
                        'url: ' . ($url) . "\n" .
                        'charset: ' . ($charset)
                    ),
                    'CurlValidator::urlCharsetSupport'
                );
            }
            return $urlEncodedInSupportCharset;
        }

        /**
         * urlContentIsNotEmpty
         * 
         * @access public
         * @static
         * @param  string $url
         * @return boolean
         */
        public static function urlContentIsNotEmpty($url)
        {
            $curler = RequestCache::read('curler');
            $urlBody = $curler->getResponse();
            if (is_null($urlBody)) {
                $urlBody = $curler->get($url);
            }
            return !empty($urlBody);
        }

        /**
         * urlContentSizeIsLessThan
         * 
         * @access public
         * @static
         * @param  string $url
         * @param  integer $maxKilobytes
         * @return boolean
         */
        public static function urlContentSizeIsLessThan($url, $maxKilobytes)
        {
            $curler = RequestCache::read('curler');
            $urlBody = $curler->getResponse();
            if (is_null($urlBody)) {
                $urlBody = $curler->get($url);
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
         * @param  string $url
         * @return boolean
         */
        public static function urlContentTypeIsHtml($url)
        {
            $headInfo = self::_getHeadInfo($url);
            return isset($headInfo['content_type'])
                && strstr(strtolower($headInfo['content_type']), 'text/html') !== false;
        }

        /**
         * urlStatusCode
         * 
         * @access public
         * @static
         * @param  string $url
         * @param  array $allowedStatusCodes (default: array(200))
         * @return boolean
         */
        public static function urlStatusCode(
            $url, array $allowedStatusCodes = array(200)
        ) {
            $headInfo = self::_getHeadInfo($url);
            return isset($headInfo['http_code'])
                && in_array($headInfo['http_code'], $allowedStatusCodes);
        }
    }
