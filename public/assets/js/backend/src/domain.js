define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'src/domain/index' + location.search,
                    add_url: 'src/domain/add',
                    edit_url: 'src/domain/edit',
                    del_url: 'src/domain/del',
                    reset_url: 'src/domain/reset',
                    multi_url: 'src/domain/multi',
                    table: 'src_domain',
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
                        {field: 'project_name', title: __('Project_name')},
                        {field: 'sub_flag', title: __('Sub_flag'), formatter: Table.api.formatter.status, custom: {is_scan: 'success', no_scan: 'gray', scan: 'info', failed: 'danger', import: 'warning'},searchList: {is_scan: '已扫描', no_scan: '未扫描', scan: '扫描中', failed: '失败'}},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
                                {name: 'add_sub', text: '', title: '添加子域名', icon: 'fa fa-ioxhost', classname: 'btn btn-xs btn-primary btn-dialog', url: 'src/domain/add_sub'}
                                ], events: Table.api.events.operate, formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        add_sub: function () {
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