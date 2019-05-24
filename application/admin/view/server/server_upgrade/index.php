{extend name="public/container"}
{block name="head_top"}
<script src="{__PLUG_PATH}moment.js"></script>
<link rel="stylesheet" href="{__PLUG_PATH}daterangepicker/daterangepicker.css">
<script src="{__PLUG_PATH}daterangepicker/daterangepicker.js"></script>
{/block}
{block name="content"}
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <button type="button" class="btn btn-w-m btn-primary" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}')">添加站点</button>
            </div>
            <div class="ibox-content">

                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline">
                            <select name="status" aria-controls="editable" class="form-control input-sm">
                                <option value="" {eq name="where.status" value=""}selected="selected"{/eq}>状态</option>
                                <option value="1" {eq name="where.status" value="1"}selected="selected"{/eq}>已授权</option>
                                <option value="0" {eq name="where.status" value="0"}selected="selected"{/eq}>未授权</option>
                            </select>
                            <div class="input-group datepicker">
                                <input style="width: 188px;" type="text" id="data" class="input-sm form-control" name="data" value="{$where.data}" placeholder="请选择日期" >
                            </div>
                            <div class="input-group">
                                <input size="26" type="text" name="name" value="{$where.name}" placeholder="请输入站点名称、IP、域名" class="input-sm form-control"> <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> <i class="fa fa-search" ></i>搜索</button> </span>
                            </div>
                        </form>
                    </div>
                </div>
                <table class="footable table table-striped  table-bordered " data-page-size="20">
                    <thead>
                    <tr>
                        <th class="text-center">编号</th>
                        <th class="text-center">IP</th>
                        <th class="text-center">HTTPS</th>
                        <th class="text-center">名称</th>
                        <th class="text-center">版本号</th>
                        <th class="text-center">首次时间</th>
                        <th class="text-center">最后访问</th>
                        <th class="text-center">授权时间</th>
                        <th class="text-center">取消时间</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody class="">
                    {volist name="list" id="vo"}
                    <tr>
                        <td class="text-center">{$vo.id}</td>
                        <td class="text-center">{$vo.ip}</td>
                        <td class="text-center">{$vo.https}</td>
                        <td class="text-center">{$vo.name}</td>
                        <th class="text-center">{$vo.version}</th>
                        <td class="text-center">{$vo.add_time|date='Y-m-d H:i:s',###}</td>
                        <td class="text-center">{$vo.last_time|date='Y-m-d H:i:s',###}</td>
                        <td class="text-center">{$vo.auth_time|date='Y-m-d H:i:s',###}</td>
                        <td class="text-center">{if $vo.unauth_time}{$vo.unauth_time|date='Y-m-d H:i:s',###}{/if}</td>
                        <td class="text-center">
                            {if $vo.status}
                            <button class="btn btn-warning btn-xs" data-id="{$vo.id}" type="button"><i class="fa fa-warning"></i>取消授权
                            </button>
                            {else}
                            <button class="btn btn-success btn-xs" data-id="{$vo.id}" type="button"><i class="fa fa-warning"></i>授权</button>
                            <button class="btn btn-delete btn-xs"  data-id="{$vo.id}"   type="button"><i class="fa fa-trash"></i>删除</button>

                            {/if}
                        </td>
                    </tr>
                    {/volist}
                    </tbody>
                </table>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    var dateInput =$('.datepicker');
    dateInput.daterangepicker({
        autoUpdateInput: false,
        "opens": "center",
        "drops": "down",
        "ranges": {
            '今天': [moment(), moment().add(1, 'days')],
            '昨天': [moment().subtract(1, 'days'), moment()],
            '上周': [moment().subtract(6, 'days'), moment()],
            '前30天': [moment().subtract(29, 'days'), moment()],
            '本月': [moment().startOf('month'), moment().endOf('month')],
            '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "locale" : {
            applyLabel : '确定',
            cancelLabel : '清空',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            format : 'YYYY/MM/DD',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                '七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
    });
    dateInput.on('cancel.daterangepicker', function(ev, picker) {
        $("#data").val('');
    });
    dateInput.on('apply.daterangepicker', function(ev, picker) {
        $("#data").val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });
    $(".btn-warning").click(function () {
        var that=this;
        $eb.layer.confirm('您确定取消授权这个站点吗？',{
            btn: ['取消授权','取消'] //按钮
        }, function(){
            $.ajax({
                type: "GET",
                url:"unshouquan/id/"+$(that).data('id'),
                dataType: 'json',
                success: function (result){
                    if(result.code==200){
                        $eb.layer.msg(result.msg,{icon:1});
                        $(that).removeClass('btn-warning').addClass('btn-success').text('已取消授权');
                    }else{
                        $eb.layer.msg(result.msg);
                    }
                }
            })
        }, function(){
            $eb.layer.msg('已取消',{icon:0});
        });
    });
    $(".btn-success").click(function () {
        var that=this;
        $eb.layer.confirm('您确定授权这个站点吗？',{
            btn: ['确认授权','取消'] //按钮
        }, function(){
            $.ajax({
                type: "GET",
                url:'shouquan/id/'+$(that).data('id'),
                dataType: 'json',
                success: function (result){
                    if(result.code==200){
                        $eb.layer.msg(result.msg,{icon:1});
                        $(that).removeClass('btn-success').addClass('btn-warning').text('取消授权');
                    }else{
                        $eb.layer.msg(result.msg);
                    }
                }
            })
        }, function(){
            $eb.layer.msg('已取消',{icon:0});
        });
    });
    $(".btn-delete").click(function () {
        var that=this;
        $eb.layer.confirm('您确定删除这个站点吗？',{
            btn: ['确认删除','取消'] //按钮
        }, function(){
            $.ajax({
                type: "GET",
                url:'delete/id/'+$(that).data('id'),
                dataType: 'json',
                success: function (result){
                    if(result.code==200){
                        $eb.layer.msg(result.msg,{icon:1});
                        $(that).removeClass('btn-delete').hide();
                        $(that).parents('tr').remove();
                    }else{
                        $eb.layer.msg(result.msg);
                    }
                }
            })
        }, function(){
            $eb.layer.msg('已取消',{icon:0});
        });
    })
</script>
{/block}

