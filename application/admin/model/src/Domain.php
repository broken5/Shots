<?php

namespace app\admin\model\src;

use think\Model;


class Domain extends Model
{





    // 表名
    protected $name = 'src_domain';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    public function existsDomain($domain_list){
        $exists_domain = [];
        $res = $this->field('domain')->where('domain','in', $domain_list)->select();
        if($res){
            foreach ($res as $v) {
                $exists_domain[] = $v->domain;
            }
        }
        return $exists_domain;
    }

}
