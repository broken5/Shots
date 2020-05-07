<?php

namespace app\admin\model\manage;

use think\Model;


class Sub extends Model
{

    

    

    // 表名
    protected $name = 'manage_sub';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function existsSubDomain($subdomain){
        $row = $this->where('subdomain', $subdomain)->find();
        if($row){
            return true;
        }
        else{
            return false;
        }
    }

    public function addSubDomian($domain, $subdomain, $ip, $city, $is_private, $is_cdn)
    {

        if($this->existsSubDomain($subdomain)){
            return false;
        }else{
            $data = [
                'domain'=>$domain,
                'subdomain'=>$subdomain,
                'subdomain_ip'=>$ip,
                'city'=>$city,
                'alivescan'=>'no_scan',
                'portscan'=>'no_scan',
            ];
            if($is_cdn){
                $data['portscan'] = 'is_cdn';
            }
            if($is_private){
                $data['alivescan'] = 'is_private';
                $data['portscan'] = 'is_private';
            }
            $this->data($data, true)->isUpdate(false)->save();
            return true;
        }
    }


}
