<?php

namespace Components;

class Component {

    protected $_config = [];
    protected $_options = [];

    public function __construct($config = [], $options=[]) {
        $this->_config = (array)$config + $this->_config;
        if (!empty($options)) {
            $this->_options = $options;
        }
    }
}