define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'manage/ports/index' + location.search,
                    add_url: 'manage/ports/add',
                    edit_url: '',
                    del_url: 'manage/ports/del',
                    multi_url: 'manage/ports/multi',
                    table: 'manage_ports',
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
                        {field: 'subdomain_ip', title: __('Subdomain_ip')},
                        {field: 'subdomain', title: __('Subdomain')},
                        {field: 'port', title: __('Port')},
                        {field: 'service', title: __('Service'), formatter: Table.api.formatter.flag},
                        {field: 'product', title: __('Product'), formatter: Table.api.formatter.flag},
                        {field: 'version', title: __('Version')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange'},
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