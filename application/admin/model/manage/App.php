<?php

namespace app\admin\model\manage;

use think\Model;


class App extends Model
{

    

    

    // 表名
    protected $name = 'manage_app';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function check_key($key, $username, $type)
    {
        $where = [
            'key'   =>  $key,
            'user'  =>  $username,
            'type'  =>  $type,
            'ip'    =>  $_SERVER['REMOTE_ADDR']
        ];
        $last = $this->where($where)->find();
        if(!$last){
            $data = [
                'user'      =>  $username,
                'key'       =>  $key,
                'type'      =>  $type,
                'status'    =>  '1',
                'ip'        =>  $_SERVER['REMOTE_ADDR']
            ];
            $this->isUpdate(false)->save($data);
        }else{
            $time = time();
            $last_update = strtotime($last['updatetime']);
            $timing = $time - $last_update;
            $data = ($timing > 480) ? ['status'=>0] : ['status'=>1];
            $this->isUpdate(true)->save($data, $where);
        }
    }
}
