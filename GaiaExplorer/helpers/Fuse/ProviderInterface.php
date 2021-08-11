<?php

namespace Gaia\Helpers\Fuse;


interface ProviderInterface
{
    /**
     * 服务参与熔断的超时时间
     * false : 不参与熔断策略
     * 0     : 使用默认超时
     * 0+    : 超时时间
     * @param $action
     * @param $arguments
     * @return false | int
     */
    function participateFuse($action, $arguments = []);

    /**
     * 根据开放度判定是否本次是否提供服务
     * @param $openness
     * @param $action
     * @param $arguments
     * @return true;
     */
    function checkInvokeAbleByOpenness($openness, $action, $arguments = []);

    /**
     * 当服务熔断状态下执行的逻辑与返回值
     * @param null $action
     * @param $arguments
     * @return mixed
     */
    function getReturnValueOnServiceClosed($action = null, $arguments = []);

    /**
     * 是否通过 InvocationWrapperHelper 来监控
     * @return bool
     */
    function monitorByInvocationWrapperHelper();
}