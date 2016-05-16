<?php

    // dependency check
    if (class_exists('Curler') === false) {
        throw new Exception(
            '*Curler* class required. Please see ' .
            'https://github.com/onassar/PHP-Curler'
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
         * charsetIsDefined
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function charsetIsDefined(Curler $curler)
        {
            $charset = $curler->getCharset();
            return $charset !== false;
        }

        /**
         * charsetIsSupported
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function charsetIsSupported(Curler $curler)
        {
            $charset = $curler->getCharset();
            $charsetIsSupported = StringValidator::inList(
                $charset,
                array(
                    'utf-8',
                    'ascii',
                    'iso-8859-1',
                    'iso-8859-15',
                    'euc-kr',
                    'euc-jp',
                    'windows-1252'
                )
            );
            if ($charsetIsSupported === true) {
                return true;
            }
            $content = $curler->getResponse();
            return mb_check_encoding($content, 'UTF-8');
        }

        /**
         * contentContainsHeadTag
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function contentContainsHeadTag(Curler $curler)
        {
            $content = $curler->getResponse();
            return $content !== false
                && $content !== null
                && strstr(strtolower($content), '<head') !== false;
        }

        /**
         * contentIsNotEmpty
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function contentIsNotEmpty(Curler $curler)
        {
            $content = $curler->getResponse();
            return $content !== false
                && $content !== null
                && empty($content) === false;
        }

        /**
         * contentTypeIsHTML
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function contentTypeIsHTML(Curler $curler)
        {
            $info = $curler->getInfo();
            $allowable = array(
                'text/html',
                'image/jpg',
                'image/jpeg',
                'image/gif'
            );
            return isset($info['content_type']) === true
                && in_array($info['content_type'], $allowable) === true;
        }

        /**
         * contentTypeIsImage
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function contentTypeIsImage(Curler $curler)
        {
            $info = $curler->getInfo();
            $allowable = array(
                'image/png',
                'image/jpg',
                'image/jpeg',
                'image/gif'
            );
            return isset($info['content_type']) === true
                && in_array($info['content_type'], $allowable) === true;
        }

        /**
         * respondsWithinTimeout
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function respondsWithinTimeout(Curler $curler)
        {
            $error = $curler->getError();
            if ($error === false) {
                return true;
            }
            return $error['code'] !== 'CURLE_OPERATION_TIMEDOUT';
        }

        /**
         * validFilesize
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function validFilesize(Curler $curler)
        {
            $error = $curler->getError();
            if ($error === false) {
                return true;
            }
            return $error['code'] !== 'CUSTOM_FILESIZE';
        }

        /**
         * validMime
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function validMime(Curler $curler)
        {
            $error = $curler->getError();
            if ($error === false) {
                return true;
            }
            return $error['code'] !== 'CUSTOM_MIME';
        }

        /**
         * validRedirects
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function validRedirects(Curler $curler)
        {
            $error = $curler->getError();
            if ($error === false) {
                return true;
            }
            return $error['code'] !== 'CURLE_TOO_MANY_REDIRECTS';
        }

        /**
         * validStatusCode
         * 
         * @access public
         * @static
         * @param  Curler $curler
         * @return boolean
         */
        public static function validStatusCode(Curler $curler)
        {
            $error = $curler->getError();
            if ($error === false) {
                return true;
            }
            return $error['code'] !== 'CUSTOM_HTTPSTATUSCODE';
        }
    }
