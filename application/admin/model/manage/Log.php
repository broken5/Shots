<?php

namespace app\admin\model\manage;

use think\Model;


class Log extends Model
{

    

    

    // 表名
    protected $name = 'manage_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function add_log($data, $type){
        $domain = $data['domain'];
        $time = $data['time'];
        $reason = $data['reason'];
        $status = $data['code'];
        $count = $data['count'];
        $log = [
            'domain'    =>  $domain,
            'count'     =>  $count,
            'timing'      =>  $time.'秒',
            'reason'    =>  $reason,
            'status'    =>  $status,
            'from'      =>  $_SERVER['REMOTE_ADDR'],
        ];
        if (in_array($type, array('subdomain', 'portscan', 'alivescan'))){
            $log['type'] = $type;
            $this->isUpdate(false)->save($log);
        }else{
            return;
        }
    }







}
