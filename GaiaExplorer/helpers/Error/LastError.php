<?php

namespace Gaia\Helpers\Error;

trait LastError
{
    public $logDisabled = false;

    /* @var Error $_lastError */
    protected $_lastError;

    /* @deprecated $error prevent access */
    private $error;

    /**
     * @return Error
     */
    public function lastError() {
        return $this->_lastError;
    }

    protected function createError($code, $msg, $msgForDeveloper, $level, $exception=null) {
        if ($exception) {
            return new ExceptionError($code, $exception, $msg, $msgForDeveloper, $level);
        }
        else {
            return new Error($code, $msg, $msgForDeveloper, $level);
        }
    }

    /**
     * @param int|string|\Exception|Error $error, error code | error message | error object
     */
    public function setLastError($error) {
        if (is_null($error)) {
            $this->_lastError = null;
            return;
        }

        $level = LOG_INFO;
        if (!is_a($error, Error::class)) {
            $stringArgs = [];
            $intArgs = [];
            $exception = null;
            $msgForDeveloper = null;
            $code = Error::ERROR_CODE_COMMON;

            foreach(func_get_args() as $arg) {
                if (is_int($arg)) {
                    $intArgs[] = $arg;
                }
                else if (is_string($arg)) {
                    $stringArgs[] = $arg;
                }
                else if ($arg instanceof \Exception || $arg instanceof \Error) {
                    $exception = $arg;
                    $level = LOG_ERR;
                }
                else if (is_array($arg)) {
                    $msgForDeveloper = $arg;
                }
                else if (!is_null($error)) {
                    $stringArgs = [];
                }
                else {
                    // @codeCoverageIgnoreStart
                    trigger_error('Invalid parameters in setLastError: ' . $error);
                }// @codeCoverageIgnoreEnd
            }

            for ($i=0; $i<2 && $i<count($intArgs); $i++) {
                if ($intArgs[$i] < 0 || $intArgs[$i] >= Error::ERROR_CODE_COMMON || (empty($stringArgs) && $i == 0) || (count($intArgs) >= 2 && $i == 0)) {
                    $code = $intArgs[$i];
                }
                else {
                    $level = $intArgs[$i];
                }
            }

            $msg = count($stringArgs)>=1?$stringArgs[0]:null;
            if (count($stringArgs)>=2) {
                if (!$msgForDeveloper) {
                    $msgForDeveloper = $stringArgs[1];
                }
                else if (is_array($msgForDeveloper)) {
                    $msgForDeveloper['msg'] = $stringArgs[1];
                }
            }

            $error = $this->createError($code, $msg, $msgForDeveloper, $level, $exception);

            $level = $error->level;
        }

        if (!isset($error->file)) {
            $backtrace = debug_backtrace();
            if ($backtrace && count($backtrace) > 1) {
//                for ($index = 1; $index<count($backtrace); $index++ ) {
//                    if (array_key_exists('file', $backtrace[$index])) {
//                        if ((strstr($backtrace[$index]['file'], '/ObjectMongoModel.php') !== FALSE && strstr($backtrace[$index]['file'], '/ObjectMongoModel.php') !== FALSE && $backtrace[$index]['function'] != 'call_user_func_array')) {
//                            break;
//                        }
//                    }
//                }

                $index = 0;

                if ($index < count($backtrace) && array_key_exists('file', $backtrace[$index])) {
                    $error->file = $backtrace[$index]['file'];
                    $error->lineNumber = $backtrace[$index]['line'];
                }
            }
        }

        if ($level <= LOG_NOTICE && !$this->logDisabled) {
            $withBacktrace = (\GaiaApp::getConfig('env') != ENV_STAGE_PRODUCTION || $level <= LOG_CRIT) && (!isset($error->backtrace) || !($error->backtrace));

            if ($withBacktrace) {
                ob_start();
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); // TODO 自己输出栈
                $error->backtrace = ob_get_contents();
                @ob_end_clean();
            }

            if (!$this->logDisabled) {
                \GaiaApp::instance()->logger('log.app_error')->jsonLog($error->toJSON(['with_backtrace'=>$withBacktrace]), ['with_backtrace'=>$withBacktrace, 'with_syslog'=>false]);
            }
        }

        $this->_lastError = $error;
    }

    /**
     * 日志模块
     * @param string $name
     * @return \Cola_Ext_Log_Abstract
     */
    protected function logger($name = 'log.default') {
        return \GaiaApp::instance()->logger($name);
    }

    public function log($type, $msg, $options=[]) {
        if (!$this->logDisabled) {
            $this->logger('log.'.$type)->jsonLog($msg, $options);
        }
    }
}
