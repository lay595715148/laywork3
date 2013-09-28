<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Service;
use Laywork,Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoService extends Service {
    public function doit() {
        Debugger::info('doit', 'DemoService', __LINE__, __METHOD__, __CLASS__);
        $bean = $this->bean;
        $fields = $bean->toInsertFields();
        $bean->setName(rand(1,99));
        $bean->setType(1);
        $values = $bean->toValues();
        $ret = $this->store->insert($bean, $fields, $values);
        Debugger::debug($ret, 'DemoService', __LINE__, __METHOD__, __CLASS__);
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray();
        $arr = $bean->rowsToArray($rows);
        Debugger::debug($arr, 'DemoService', __LINE__, __METHOD__, __CLASS__);
        
        $tyf = $bean->toField('type');
        $tyv = $bean->getType();
        $naf = $bean->toField('name');
        $bean->setName('ob');
        $values = $bean->toValues();
        $ret = $this->store->update($bean, array($naf), $values, array($tyf => $tyv));
        Debugger::debug($ret, 'DemoService', __LINE__, __METHOD__, __CLASS__);
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray(2);
        $arr = $bean->rowsToArray($rows);
        Debugger::debug($arr, 'DemoService', __LINE__, __METHOD__, __CLASS__);
        
        $tyf = $bean->toField('type');
        $tyv = $bean->getType();
        $ret = $this->store->delete($bean, array($tyf => $tyv));
        Debugger::debug($ret, 'DemoService', __LINE__, __METHOD__, __CLASS__);
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray();
        $arr = $bean->rowsToArray($rows);
        Debugger::debug($arr, 'DemoService', __LINE__, __METHOD__, __CLASS__);
        
        return $arr;
    }
}
?>