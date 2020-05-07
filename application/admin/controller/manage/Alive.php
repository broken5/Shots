<?php

namespace app\admin\controller\manage;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Alive extends Backend
{
    
    /**
     * Alive模型对象
     * @var \app\admin\model\manage\Alive
     */
    protected $model = null;


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\manage\Alive;

    }

    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if(!$row){
            $this->error('不存在此条记录');
        }
        $this->view->assign('row', $row);
        $subdomain = $row->subdomain;
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $url = $params['url'] ? trim($params['url'], '/') : null;
                $host = parse_url($url)['host'];
                if($host && is_domain($host) && $host==$subdomain){
                    $title = @$params['title'];
                    $size = @$params['size'];
                    $status = @$params['status'];
                    $fingerprint = @$params['fingerprint'];
                    $where = ['id'=>$ids];
                    $data = [
                        'url'=>$url,
                        'title'=>$title,
                        'size'=>$size,
                        'status'=>$status,
                        'fingerprint'=>$fingerprint,
                    ];
                    $this->model->isUpdate(true)->save($data, $where);
                    $this->success('编辑成功');
                }else{
                    $this->error('该URL不属于这个子域名');
                }
            }
            $this->error();
        }
        return $this->view->fetch();
    }

    public function add()
    {
    }
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
