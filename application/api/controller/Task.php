<?php

namespace app\api\controller;

use app\admin\model\manage\Alive;
use app\common\controller\Api;
use app\admin\model\manage\Sub;
use app\admin\model\src\Domain;
use app\admin\model\manage\Ports;
use app\admin\model\manage\Log;
use app\admin\model\manage\App;

/**
 * 示例接口
 */
class Task extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    /**
     * 测试方法
     *
     * @ApiTitle    (测试名称)
     * @ApiSummary  (测试描述信息)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/demo/test/id/{id}/name/{name})
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="id", type="integer", required=true, description="会员ID")
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     */
    public function test()
    {
        $this->success('返回成功', $this->request->param());
    }

    /**
     * 下发子域名收集任务
     *  return  {"code":1,"msg":"返回成功","time":"1588314390","data":{"domain":"ceshi.com"}}
     *
     */
    public function getDomain()
    {
        if($this->request->isPost()){
            (new App())->check_key($this->request->get('key'), $this->username, 'subdomain');
            // 获取未扫描子域的主域名
            $tmp = (new Domain())->where('sub_flag', 'no_scan')->find();
            if(!$tmp){
                $this->error('无数据', []);
            }else{
                $domain = $tmp->domain;

                // 更新子域名扫描状态为扫描中
                $data = ['sub_flag'=>'scan', 'updatetime'=>time()];
                (new Domain())->where('domain', $domain)->update($data);
                $this->success('返回成功', ["domain"=>$domain]);
            }

        }
    }

    /**
     * 上传子域名数据
     *  $domain_data = {"domain": "ceshi1.com","data":[{"subdomain": "www.ceshi1.com","subdomain_ip": "123.123.123.123","city": "Shanghai"}]}
     *
     */
    public function putSubDomain(){
        $sub = new Sub();
        if($this->request->isPost()){
            $domain_data = json_decode(file_get_contents('php://input'), true);
            if($domain_data and $domain_data['code'] == 1 and $domain_data['domain']){
                $domain = $domain_data['domain'];
                $domain_data['count'] = 0;

                // 子域名入库
                foreach ($domain_data['data'] as $v){
                    $subdomain = $v['subdomain'];
                    $ip = $v['subdomain_ip'];
                    $city = $v['city'];
                    $is_private = $v['is_private'];
                    $is_cdn = $v['is_cdn'];
                    if(substr($subdomain, strpos($subdomain, '.')+1)==$domain){
                        $sub->addSubDomian($domain, $subdomain, $ip, $city, $is_private, $is_cdn);
                        $domain_data['count'] += 1;
                    }
                }

                // 更新域名扫描状态
                $data = ['sub_flag'=>'is_scan', 'updatetime'=>time()];
                (new Domain())->where(['domain'=>$domain])->update($data);

                // 存储上传数据成功日志
                (new Log())->add_log($domain_data, 'subdomain');
            }elseif($domain_data['code']==0 and $domain_data['domain']){
                // 存储上传数据失败日志
                $domain_data['count'] = 0;
                (new Log())->add_log($domain_data, 'subdomain');
            }

            $this->success('返回成功', ['domain_data'=>$domain_data]);
        }
    }

    /**
     * 下发端口扫描任务
     *  {"code":1,"msg":"返回成功","time":"1588317437","data":{"subdomain":"www.ceshi.com","ip": "123.123.123.123"}}
     */
    function getIp(){
        if($this->request->isPost()){
            (new App())->check_key($this->request->get('key'), $this->username, 'portscan');
            // 获取未扫描端口的域名
            $tmp = (new Sub())->where('portscan', 'in', ['no_scan', '', null])->find();
            if(!$tmp){
                $this->error('无数据', []);
            }else{
                $ip = $tmp->subdomain_ip;
                $subdomain = $tmp->subdomain;

                // 更新端口扫描状态为正在扫描
                $data = ['portscan'=>'scan', 'updatetime'=>time()];
                (new Sub())->where('subdomain', $subdomain)->update($data);

                // 查询此IP的端口数据
                $port_data = (new Ports())
                    ->where('subdomain_ip',$ip)
                    ->field(['subdomain_ip', 'port','service', 'product', 'version'])
                    ->select();

                // 如果此IP被扫描过，就直接跳过该IP
                if($port_data){
                    foreach (collection($port_data)->toArray() as $v){
                        $ip = $v['subdomain_ip'];
                        $port = $v['port'];
                        $service = $v['service'];
                        $product = $v['product'];
                        $version = $v['version'];
                        (new Ports())->addPorts($subdomain, $ip, $port, $service, $product, $version);
                    }

                    // 更新端口状态为已扫描
                    $data = ['portscan'=>'is_scan', 'updatetime'=>time()];
                    (new Sub())->where('subdomain', $subdomain)->update($data);

                    // 告诉端口扫描脚本该IP已经被扫描过
                    $this->success('返回成功', ["subdomain"=>$subdomain, "ip"=>$ip, "is_scan"=> true]);
                }else{
                    // 告诉端口扫描脚本该IP没有被扫描过
                    $this->success('返回成功', ["subdomain"=>$subdomain, "ip"=>$ip, "is_scan"=> false]);
                }
            }
        }
    }

    /**
     * 上传端口数据
     * $port_data = {"subdomain": "ceshi.com","data": [{"ip": "", "port":""....}]}
     */
    public function putPort(){
        if($this->request->isPost()){
            $port_data = json_decode(file_get_contents('php://input'), true);
            if($port_data and $port_data['code']==1 and $port_data['domain']){
                $subdomain = $port_data['domain'];
                $port_data['count'] = 0;

                // 存储上传的端口数据
                foreach ($port_data['data'] as $v){
                    $ip = $v['ip'];
                    $port = intval($v['port']);
                    $service = $v['service'];
                    $product = $v['product'];
                    $version = $v['version'];
                    if(is_ip($ip) && $port>1 && $port<65536){
                        (new Ports())->addPorts($subdomain, $ip, $port, $service, $product, $version);
                    }
                    $port_data['count'] += 1;
                }

                // 存储上传数据成功日志
                (new Log())->add_log($port_data, 'portscan');

                // 更新端口扫描状态为已扫描
                $data = ['portscan'=>'is_scan', 'updatetime'=>time()];
                (new Sub())->where(['subdomain'=>$subdomain])->update($data);
            }elseif ($port_data['code']==0){
                // 存储上传数据失败日志
                $port_data['count'] = 0;
                (new Sub())->where(['subdomain'=>$port_data['domain']])->update(['portscan'=>'failed']);
                (new Log())->add_log($port_data, 'portscan');
            }
            $this->success('返回成功', ['port_data'=>$port_data]);
        }
    }


    /**
     * 下发存活扫描任务
     *  {"code":1,"msg":"返回成功","time":"1588329351","data":{"subdomain":"www.ceshi.com"}}
     */
    public function getSubdomain(){
        (new App())->check_key($this->request->get('key'), $this->username, 'alivescan');
        if($this->request->isPost()){
            // 获取未扫描存活的域名
            $tmp = (new Sub())->where('alivescan', 'no_scan')->find();
            if(!$tmp){
                $this->error('无数据', []);
            }else{
                // 更新存活扫描状态为正在扫描
                $subdomain = $tmp->subdomain;
                $data = ['alivescan'=>'scan', 'updatetime'=>time()];
                (new Sub())->where('subdomain', $subdomain)->update($data);
                $this->success('返回成功', ["subdomain"=>$subdomain]);
            }
        }
    }


    /**
     * 上传存活数据
     *  {"subdomain": "newmedia.chinapost.com.cn", "data": [{"url": "http://www.ceshi.com/", ...}]}
     */
    public function putAlive(){
        if($this->request->isPost()){
            $alive_data = json_decode(file_get_contents('php://input'), true);
            if($alive_data and $alive_data['code']==1 and $alive_data['domain']){
                $subdomain = $alive_data['domain'];
                $alive_data['count'] = 0;
                // 存储上传的存活数据
                foreach ($alive_data['data'] as $v){
                    $url = $v['url'];
                    $title = $v['title'];
                    $status = $v['status'];
                    $size = $v['size'];
                    $fingerprint = $v['fingerprint'];
                    if(is_url($url)){
                        (new Alive())->addAlive($subdomain, $url, $title, $size, $status, $fingerprint);
                    }
                    $alive_data['count'] += 1;
                }
                // 更新存活扫描状态为已扫描
                $data = ['alivescan'=>'is_scan', 'updatetime'=>time()];
                (new Sub())->where(['subdomain'=>$subdomain])->update($data);

                // 存储上传数据成功日志
                (new Log())->add_log($alive_data, 'alivescan');
            }elseif ($alive_data['code'] == 0){
                // 存储上传数据失败日志
                $alive_data['count'] = 0;
                (new Sub())->where(['subdomain'=>$alive_data['domain']])->update(['alivescan'=>'failed']);
                (new Log())->add_log($alive_data, 'alivescan');
            }
            $this->success('返回成功', ['alive_data'=>$alive_data]);
        }

    }

    /**
     * 获取用户批量添加的子域名
     *
     */

    public function updateIP2Domain(){
        if($this->request->isPost()) {
            $domain_data = json_decode(file_get_contents('php://input'), true);
            $domain = $domain_data['domain'];
            $ip = $domain_data['ip'];
            $city = $domain_data['city'];
            $is_cdn = $domain_data['is_cdn'];
            $is_private = $domain_data['is_private'];
            $data = [
                'subdomain_ip'=>$ip,
                'city'=>$city,
            ];
            if($is_private){
                $data['portscan'] = 'is_private';
                $data['alivescan'] = 'is_private';
            }else if($is_cdn){
                $data['portscan'] = 'is_cdn';
                $data['alivescan'] = 'no_scan';
            }else{
                $data['alivescan'] = 'no_scan';
            }
            if((new Sub())->isUpdate(true)->save($data, ['subdomain'=>$domain])) {
                $this->success('更新成功');
            }else{
                $this->error('未找到数据');
            }
        }
    }

    public function demo(){
        var_dump(is_domain('test.www-test.com.cn'));

        # var_dump(is_url('http://beebox-admin.183gz.com.cn/download/beeboxApp-info.html'));
    }

}
