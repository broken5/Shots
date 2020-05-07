define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'manage/alive/index' + location.search,
                    add_url: 'manage/alive/add',
                    edit_url: 'manage/alive/edit',
                    del_url: 'manage/alive/del',
                    multi_url: 'manage/alive/multi',
                    table: 'manage_alive',
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
                        {field: 'subdomain', title: __('Subdomain')},
                        {field: 'url', title: __('Url'), formatter: Table.api.formatter.url},
                        {field: 'title', title: __('Title')},
                        {field: 'size', title: __('Size')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.flag, custom: {'200': 'success', '404': 'danger', '403': 'warning'}},
                        {field: 'fingerprint', title: __('fingerprint')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});