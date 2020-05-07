<?php

namespace app\admin\model\manage;

use think\Model;


class Ports extends Model
{

    

    

    // 表名
    protected $name = 'manage_ports';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    
    public function addPorts($subdomain, $ip, $port, $service, $product, $version){
        $row = $this->where(['subdomain'=>$subdomain, 'subdomain_ip'=>$ip, 'port'=>$port])->find();
        if($row){
            return false;
        }else{
            $data = [
                'subdomain'=>$subdomain,
                'subdomain_ip'=>$ip,
                'port'=>$port,
                'service'=>$service,
                'product'=>$product,
                'version'=>$version,
            ];
            $this->save($data);
            return true;
        }
    }
    




}
