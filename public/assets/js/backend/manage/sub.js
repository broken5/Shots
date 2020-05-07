define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'manage/sub/index' + location.search,
                    add_url: 'manage/sub/add',
                    edit_url: 'manage/sub/edit',
                    del_url: 'manage/sub/del',
                    reset_url: 'manage/sub/reset',
                    multi_url: 'manage/sub/multi',
                    table: 'manage_sub',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'domain', title: __('Domain')},
                        {field: 'subdomain', title: __('Subdomain')},
                        {field: 'subdomain_ip', title: __('Subdomain_ip')},
                        {field: 'city', title: __('City')},
                        {field: 'alivescan', title: __('Alivescan'), formatter: Table.api.formatter.status, custom: {is_scan: 'success', no_scan: 'danger', scan: 'info', failed: 'danger'},searchList: {is_scan: '已扫描', no_scan: '未扫描', scan: '扫描中', is_private: '跳过', 'failed': '失败'}},
                        {field: 'portscan', title: __('Portscan'), formatter: Table.api.formatter.status, custom: {is_scan: 'success', no_scan: 'danger', scan: 'info', failed: 'danger'},searchList: {is_scan: '已扫描', no_scan: '未扫描', scan: '扫描中', is_private: '跳过', is_cdn: '跳过', 'failed': '失败'}},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
                            {name: 'add_ports', text: '添加端口', title: '添加端口', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'manage/sub/add_ports'},
                            {name: 'add_alive', text: '添加存活', title: '添加存活', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'manage/sub/add_alive'}
                            ], events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        add_ports: function () {
            Controller.api.bindevent();
        },
        add_alive: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});