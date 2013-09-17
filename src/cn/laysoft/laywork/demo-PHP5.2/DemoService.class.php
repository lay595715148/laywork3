<?php
if(!defined('INIT_LAYWORK')) { exit; }

class DemoService extends Service {
    public function doit() {
        $bean = $this->bean;
        $fields = $bean->toInsertFields();
        $bean->setName(rand(1,99));
        $bean->setType(1);
        $values = $bean->toValues();
        $ret = $this->store->insert($bean, $fields, $values);
        echo '<pre>';print_r($ret);echo '</pre>';
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray();
        $arr = $bean->rowsToArray($rows);
        echo '<pre>';print_r($arr);echo '</pre>';
        
        $tyf = $bean->toField('type');
        $tyv = $bean->getType();
        $naf = $bean->toField('name');
        $bean->setName('ob');
        $values = $bean->toValues();
        $ret = $this->store->update($bean, array($naf), $values, array($tyf => $tyv));
        echo '<pre>';print_r($ret);echo '</pre>';
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray(2);
        $arr = $bean->rowsToArray($rows);
        echo '<pre>';print_r($arr);echo '</pre>';
        
        $tyf = $bean->toField('type');
        $tyv = $bean->getType();
        $ret = $this->store->delete($bean, array($tyf => $tyv));
        echo '<pre>';print_r($ret);echo '</pre>';
        
        $fields = $bean->toFields();
        $ret = $this->store->select($bean, $fields);
        $rows = $this->store->toArray();
        $arr = $bean->rowsToArray($rows);
        echo '<pre>';print_r($arr);echo '</pre>';
        
        return $arr;
    }
}
?>