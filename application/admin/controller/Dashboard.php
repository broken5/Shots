<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use app\admin\model\src\Project;
use app\admin\model\src\Domain;
use app\admin\model\manage\Sub;
use app\admin\model\manage\Log;
use app\admin\model\manage\Alive;
use app\admin\model\manage\Ports;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        $seventtime = \fast\Date::unixtime('day', -7);
        $sublist = $createlist = [];
        for ($i = 0; $i < 7; $i++)
        {
            $day = date("Y-m-d", mktime(0,0,0,date('m'),date('d')-4+$i,date('Y')));
            $time_1 = mktime(0,0,0,date('m'),date('d')-4+$i,date('Y'));
            $time_2 = mktime(0,0,0,date('m'),date('d')-4+$i+1,date('Y'));
            $sublist[$day] = Sub::where("createtime > {$time_1} and createtime < {$time_2}")->count();
        }
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        $totalproject = Project::count();
        $totaldomain = Domain::count();
        $totalsub = Sub::count();
        $totalports = Ports::count();
        $totalalive = Ports::count();
        $today=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $todaydomain = Domain::where('createtime', '>', $today)->count();
        $todaysub = Sub::where('createtime', '>', $today)->count();
        $todayports = Ports::where('createtime', '>', $today)->count();
        $todayalive = Alive::where('createtime', '>', $today)->count();
        $this->view->assign([
            'totalproject'       => $totalproject,
            'totaldomain'        => $totaldomain,
            'totalsub'           => $totalsub,
            'totalports'         => $totalports,
            'totalalive'         => $totalalive,
            'todaydomain'        => $todaydomain,
            'todaysub'           => $todaysub,
            'todayports'         => $todayports,
            'todayalive'         => $todayalive,
            'paylist'            => $sublist
        ]);

        return $this->view->fetch();
    }

}
