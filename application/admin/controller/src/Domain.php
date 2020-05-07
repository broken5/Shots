<?php

namespace app\admin\controller\src;

use app\common\controller\Backend;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Domain extends Backend
{

    /**
     * Domain模型对象
     * @var \app\admin\model\src\Domain
     */
    protected $model = null;
    protected $project_model = null;
    protected $sub_model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\src\Domain;
        $this->project_model = new \app\admin\model\src\Project;
        $this->sub_model = new \app\admin\model\manage\Sub;
        $projectdata = $this->project_model->getProjectData();
        $this->view->assign('projectdata', $projectdata);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $domain = str_replace("\r", '', $params['domain']);
                $project_name = $params['project_name'];
                $domain_list = explode("\n", $domain);
                foreach($domain_list as $k => $v){
                    $domain_list[$k] = strtolower(trim($v));
                }
                $exists_domain = $this->model->existsDomain($domain_list);
                $new_domain = array_diff($domain_list, $exists_domain);
                foreach ($new_domain as $v){
                    if(is_domain($v)) {
                        $data = [
                            'domain'=>$v,
                            'project_name'=>$project_name,
                            'sub_flag'=>'no_scan',
                        ];
                        $this->model->data($data, true)->isUpdate(false)->save();
                    }
                }
                $this->success('任务添加完成');
            }
            $this->error();
        }
        return $this->view->fetch();
    }


    public function add_sub($ids = null)
    {
        $row = $this->model->get($ids);
        if(!$row){
            $this->error('不存在此条记录');
        }
        $domain = $row->domain;
        $this->view->assign('domain', $domain);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $subdomain = @is_domain($params['subdomain']) ? strtolower($params['subdomain']) : null;
                $ip = @is_ip($params['ip']) ? $params['ip'] : null;
                $city = @$params['city'] ? $params['city'] : null;
                if(!$ip){
                    $this->error('请输入正确的IP地址');
                }
                if (substr($subdomain, strpos($subdomain, '.')+1)!=$domain){
                    $this->error("该子域名不属于$domain!");
                }
                $row = $this->sub_model->addSubDomian($domain, $subdomain, $ip, $city);
                if($row){
                    $this->success('添加子域名成功');
                }else{
                    $this->error('该子域名已存在');
                }
            }
            $this->error();
        }
        return $this->view->fetch();
    }

    public function reset($ids = null)
    {
        if($ids){
            $where['id'] = ['in', $ids];
            $data = ['sub_flag'=>'no_scan'];
            $this->model->isUpdate(true)->save($data, $where);
            $this->success('重置成功');
        }else{
            $this->error(__('You have no permission'));
        }
    }
}
