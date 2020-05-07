<?php

namespace app\admin\model\manage;

use think\Model;


class Alive extends Model
{

    

    

    // 表名
    protected $name = 'manage_alive';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function existsUrl($url)
    {
        $row = $this->where('url', trim($url))->find();
        if($row){
            return true;
        }else{
            return false;
        }
    }


    public function addAlive($subdomain, $url, $title, $size, $status, $fingerprint)
    {
        $data = [
            'subdomain'=>$subdomain,
            'url'=>trim($url),
            'title'=>$title,
            'size'=>$size,
            'status'=>$status,
            'fingerprint'=>$fingerprint,
        ];
        if($this->existsUrl($url)){
            return false;
        }else{
            $this->data($data, true)->isUpdate(false)->save();
            return true;
        }
    }

}
