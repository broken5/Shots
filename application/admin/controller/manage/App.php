<?php

namespace app\admin\controller\manage;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class App extends Backend
{
    
    /**
     * App模型对象
     * @var \app\admin\model\manage\App
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\manage\App;

    }


    public function add()
    {
    }

    public function edit($ids = "")
    {
    }

    public function index()
    {
        if($this->request->isAjax()){
            $time = time();
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $new_list = [];
            foreach ($list as  $v){
                $timing = $time - strtotime($v['updatetime']);
                $status = ($timing>480) ? 0 : 1;
                $data = [
                    'id'            =>  $v['id'],
                    'user'          =>  $v['user'],
                    'key'           =>  $v['key'],
                    'ip'            =>  $v['ip'],
                    'type'          =>  $v['type'],
                    'status'        =>  $status,
                    'createtime'    =>  $v['createtime'],
                    'updatetime'    =>  $v['updatetime']
                ];
                $new_list[] = $data;
            }
            $total = count($new_list);
            return json(array("total" => $total, "rows" => $new_list));
        }
        return $this->view->fetch();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
