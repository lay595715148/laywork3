<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Service;
use Laywork, Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoService extends Service {
    public function __call($method, $arguments) {
        if(!method_exists($this, $method)) {
            echo "Using DemoService,please check your action-service configuration\n<br>";
        }
    }
    public function doit() {
        Debugger::info('doit', 'DemoService');
        $bean = $this->bean;
        $fields = $bean->toInsertFields();
        $bean->setName(rand(1,99));
        $bean->setType(1);
        $values = $bean->toValues();
        $ret = $this->store->insert($bean, $fields, $values);
        Debugger::debug($ret, 'DemoService');
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray();
        $arr = $bean->rowsToArray($rows);
        Debugger::debug($arr, 'DemoService');
        
        $tyf = $bean->toField('type');
        $tyv = $bean->getType();
        $naf = $bean->toField('name');
        $bean->setName('ob');
        $values = $bean->toValues();
        $ret = $this->store->update($bean, array($naf), $values, array($tyf => $tyv));
        Debugger::debug($ret, 'DemoService');
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray(2);
        $arr = $bean->rowsToArray($rows);
        Debugger::debug($arr, 'DemoService');
        
        $tyf = $bean->toField('type');
        $tyv = $bean->getType();
        $ret = $this->store->delete($bean, array($tyf => $tyv));
        Debugger::debug($ret, 'DemoService');
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray();
        $arr = $bean->rowsToArray($rows);
        Debugger::debug($arr, 'DemoService');
        
        return $arr;
    }
}
?>