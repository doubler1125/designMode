<?php
namespace Gaia\Components\Queue;

use Components\Component;

abstract class Queue extends Component
{
    protected $dumper = null;

    /**
     * 返回队列生产落盘操作类，注意，只要配置了就会直接落盘
     * @return AbstractDumper|false
     */
    public function dumper()
    {
        if (is_null($this->dumper)) {
            if (isset($this->_config['dumper']) && \GaiaApp::instance()->queueDumper($this->_config['dumper'])) {
                $this->dumper = \GaiaApp::instance()->queueDumper($this->_config['dumper']);
            } else {
                $this->dumper = false;
            }
        }
        return $this->dumper;
    }
}