define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'manage/log/index' + location.search,
                    add_url: 'manage/log/add',
                    edit_url: 'manage/log/edit',
                    del_url: 'manage/log/del',
                    multi_url: 'manage/log/multi',
                    table: 'manage_log',
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
                        {field: 'count', title: __('Count')},
                        {field: 'timing', title: __('Timing')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.flag, custom: {'1': 'success', '0': 'danger'},searchList: {'1': '成功', '0': '失败'}},
                        {field: 'from', title: __('From')},
                        {field: 'reason', title: __('Reason')},
                        {field: 'type', title: __('Type'), formatter: Table.api.formatter.flag},
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