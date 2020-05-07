<?php

namespace app\admin\model\src;

use think\Model;


class Project extends Model
{
    // 表名
    protected $name = 'src_project';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    function getProjectData()
    {
        $list = $this->field('project_name')->select();
        $projectdata = [];
        // collection($list)->toArray();
        foreach ($list as $k => $v){
            $v = $v['project_name'];
            $projectdata[$v] = $v;
        }
        return $projectdata;
    }
    



}
