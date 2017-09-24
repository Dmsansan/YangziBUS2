﻿<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>轮胎参数管理</title>
<link href="../lib/ligerUI/skins/Aqua/css/ligerui-all.css" rel="stylesheet" type="text/css" />
<link href="../lib/ligerUI/skins/ext/css/ligerui-fix.css" rel="stylesheet" type="text/css" />
<link href="../css/input.css" rel="stylesheet" />
<script src="../lib/jquery/jquery-1.9.0.min.js" type="text/javascript"></script> 
<script src="../lib/json2.js" type="text/javascript"></script>
<script src="../lib/ligerUI/js/core/base.js" type="text/javascript"></script>   
<script src="../lib/ligerUI/js/core/inject.js" type="text/javascript"></script>   
<script src="../lib/ligerUI/js/ligerui.all.js" type="text/javascript"></script>        
<script src="../lib/ligerUI/js/plugins/ligerGrid.js" type="text/javascript"></script> 
<script src="../lib/ligerUI/js/plugins/ligerLayout.js" type="text/javascript"> </script> 
<script src="../js/XHD.js" type="text/javascript"> </script>
<script src="../lib/jquery.form.js" type="text/javascript"> </script>
<script src="../CheckOper.php" type="text/javascript"> </script>
</head>

 <script type="text/javascript">
	var manager;
	var menu;
	function EditRow()
	{
		var row = manager.getSelectedRow();
            if (!row) { alert('请选择行'); return; }
			alert(row.userid);
            alert(JSON.stringify(row));  //这里只有一条记录，打开编缉的页面编辑吧
	}
	
	 function trim(str){ //删除左右两端的空格
　　     return str.replace(/(^\s*)|(\s*$)/g, "");
　　 }
　　 function ltrim(str){ //删除左边的空格
　　     return str.replace(/(^\s*)/g,"");
　　 }
　　 function rtrim(str){ //删除右边的空格
　　     return str.replace(/(\s*$)/g,"");
　　 }

	
	function deleteRow()
       { 
            //manager.deleteSelectedRow();
			//f_getChecked();
			var sid=checkedCustomer.join(',');
			var row=manager.getSelectedRow();
			if (row) {
                $.ligerDialog.confirm("记录删除无法恢复，确定删除？", function (yes) {
                    if (yes) {
                        $.ajax({
                            url: "../ajaction/v1/?menuid=111010&cmd=del", type: "POST",
                            data: { tire_param_id: row.tire_param_id, rnd: Math.random() },
                            success: function (responseText) {
								
								//
								responseText=trim(responseText);
								//top.$.ligerDialog.error(responseText);
								if(typeof(responseText)=="undefined" || responseText=="" || responseText==null){
										//服务器没有数据反回
										top.$.ligerDialog.error("未知错误");
								}else{
									var dataObj = eval("("+responseText+")");
									//alert(JSON2.stringify(dataObj));
									if (dataObj.status == "OK") {
										f_reload();
									}
									else {
										top.$.ligerDialog.error(dataObj.reason);
									}
								}
                            },
                            error: function () {
                                top.$.ligerDialog.error('删除失败!');
                            }
                        });
                    }
                })
            }
            else {
                $.ligerDialog.warn("请选择记录");
            }
			
			
       }
	   
	   //编缉
	   function edit() {
            //var manager = $("#maingrid4").ligerGetGridManager();
            var row = manager.getSelectedRow();
            if (row) {
                f_openWindow('module_11/sys.tireparam_add.php?r_tire_param_id=' +row.tire_param_id, "修改记录", 780, 490, f_save);
            } else {
                $.ligerDialog.warn('请选择行！');
            }
        }
	
 		      //工具条事件
		function itemclick(item) {
			switch (item.id) {
				case "add":
                  //var selected = grid.getSelected();
                  //if (!selected) { LG.tip('请选择行!'); return }
                  //alert("增加");
				   f_openWindow("module_11/sys.tireparam_add.php", "新增轮胎参数", 780, 300, f_save);
                  break;
				case "clearbtn":
					deleteRow();
                  //var selected = grid.getSelected();
                  //if (!selected) { LG.tip('请选择行!'); return }
                  //top.f_addTab(null, '查看', 'watch.aspx?No=1');  // 增加新标签，并打开新页
                  
				  break;
				case "edit":
					//修改
					edit();
					break;
				case "searchbtn":
					serchpanel();
                  break;
 
          }
      }
		//处理保存
	    function f_save(item, dialog) {
            var issave = dialog.frame.f_save();
            if (issave!="") {
                				
				$.ajax({
                    url: "../ajaction/v1/?menuid=111010",
                    type: "POST",
                    data: issave,					
                    beforesend: function () {
                        top.$.ligerDialog.waitting('数据保存中,请稍候...');
                    },
                    success: function (responseText) {
                        //return "ok";
						top.$.ligerDialog.closeWaitting();
						responseText=$.trim(responseText);
			
						if(typeof(responseText)=="undefined" || responseText=="" || responseText==null){
						//服务器没有数据反回
							top.$.ligerDialog.error("未知错误");
							//f_error();
						}else{
							var dataObj = eval("("+responseText+")");
									
							if (dataObj.status == "OK") {
									dialog.close();
									f_reload();
							}else {
								top.$.ligerDialog.error(dataObj.reason);
								//f_error();
							}
						}
                    },
                    error: function () {
                        top.$.ligerDialog.error('操作失败！');
                    },
                    complete: function () {
                        top.$.ligerDialog.closeWaitting();
                    }
                });
            }
        }
	  
	  function f_reload() {
            var manager = $("#maingrid4").ligerGetGridManager();
            manager.loadData(true);
        };
		
		function doclear() {
            $("input:hidden", "#serchform").val("");
            $("input:text", "#serchform").val("");
            $(".l-selected").removeClass("l-selected");
        }
		
		function doserch() {
            var sendtxt = "&rnd=" + Math.random();
            var serchtxt = $("#serchform :input").fieldSerialize() + sendtxt;
			//alert($('#company_name').val());
			//serchtxt=encodeURI($('#company_name').val());
			$('.pcontrol input', manager.toolbar).val(1);
			manager.changePage('input');
			manager.set({url:'../ajaction/v1/?menuid=111010&cmd=qry&'+serchtxt});
			manager.loadData(true);
        }
	
		function toolbar() {
				//这里需要改成根据用户权限来获取
                var toolbarOptions = {
          				items: [
            		{ text:'增加',id:'add', click:itemclick,img:"../lib/ligerUI/skins/icons/add.gif",disable:CheckOper("添加") },
            		{ line:true},
					{ text:'编缉',id:'edit',click:itemclick,img:"../lib/ligerUI/skins/icons/edit.gif",disable:CheckOper("修改") },
            		{ line:true},
            		{ text:'删除',id:'clearbtn',click:itemclick,img:"../lib/ligerUI/skins/icons/candle.gif",disable:CheckOper("删除") },
					{ line:true},
					{ text:'查询',id:'searchbtn',click:itemclick,img:"../lib/ligerUI/skins/icons/search.gif" },
					]};
				$("#toolbar").ligerToolBar({items: toolbarOptions.items});
				menu = $.ligerMenu({ width: 120, items:toolbarOptions.items});         

           }
 		
        $(function () {
				
				  
                //$("#grid").height(document.documentElement.clientHeight - $(".toolbar").height());
				$('form').ligerForm();
				toolbar();
				//
				serchpanel();
				  
				  
			/*
			tire_param_id	int auto_increment,
	company_name	varchar(40)			comment '制造商',
	brand_id		int 				comment '品牌ID',
	norms_id		int 				comment '规格ID',
	class_id        int   				comment '层级ID',
	figure_id		int					comment  '花纹ID',
	pressure_ul     int                 comment '胎压上限',
    pressure_ll     int                 comment '胎压下限',
	temp_ul         int                 comment '温度上限',
	tkph_val		int                 comment 'TKPH值',
	baro_val		int                 comment '标准充气压力',
			*/	  
            
            manager=$("#maingrid4").ligerGrid({
                checkbox: false,
                columns: [
                { display: '编号', name:'tire_param_id', align: 'left', width: 60 },
                { display: '制造商', name:'company_name', width:150,isSort:false },
				{ display: '品牌', name:'brand_name', width: 150 },
				{ display: '规格',name:'norms_name',width: 120},
				{ display: '层级',name:'class_name',width: 100},
				{ display: '花纹',name:'figure_name',width: 100},
				{ display: '胎压下限',name:'pressure_ll',width: 100},
				{ display: '胎压上限',name:'pressure_ul',width: 100},
				{ display: '速度上限',name:'speed_ul',width: 100},
				{ display: '温度上限',name:'temp_ul',width: 100},
				{ display: 'TKPH值',name:'tkph_val',width: 100},
				{ display: '标准充气压力',name:'baro_val',width: 100},
				{ display: '一保',name:'mainterance1',width: 100},
				{ display: '二保',name:'mainterance2',width: 100},
					{display:'额定里程',name:'rated_mile',width:100}
				
				], pageSize:10,
                url:'../ajaction/v1/?menuid=111010&cmd=qry&t=1',
				/*url:'../ajaction/sysaction/sys.roles_grid_show.php?a=3',*/
                /*toolbar:toolbarOptions,*/
                width: '100%',height:'97%',
				dataAction: 'server', //服务器排序
                usePager: true,       //服务器分页
				onSuccess:f_onSucess,
				onError:f_onError,
				isChecked: f_isChecked, 
				/*
				onCheckRow: f_onCheckRow, 
				onCheckAllRow: f_onCheckAllRow,*/
				onContextmenu : function (parm,e)
                {
                    //actionCustomerID = parm.data.CustomerID;
                    menu.show({ top: e.pageY, left: e.pageX });
                    return false;
                }
            });
			
            
        });
		$("#pageloading").hide(); 
		function f_onSucess(data,grid)
		{
			$("#pageloading").hide(); 
			//alert("加载完成");
		}
		function f_onError(req,status,e)
		{
			var s=status+" : "+req.status+","+req.statusText;
			alert(s);
		}
		
		function f_onCheckAllRow(checked)
        {
            for (var rowid in this.records)
            {
                if(checked)
                    addCheckedCustomer(this.records[rowid]['userid']);
                else
                    removeCheckedCustomer(this.records[rowid]['userid']);
            }
        }
 
        /*
        该例子实现 表单分页多选
        即利用onCheckRow将选中的行记忆下来，并利用isChecked将记忆下来的行初始化选中
        */
        var checkedCustomer = [];
        function findCheckedCustomer(userid)
        {
            for(var i =0;i<checkedCustomer.length;i++)
            {
                if(checkedCustomer[i] == userid) return i;
            }
            return -1;
        }
        function addCheckedCustomer(userid)
        {
            if(findCheckedCustomer(userid) == -1)
                checkedCustomer.push(userid);
        }
        function removeCheckedCustomer(userid)
        {
            var i = findCheckedCustomer(userid);
            if(i==-1) return;
            checkedCustomer.splice(i,1);
        }
        function f_isChecked(rowdata)
        {
            if (findCheckedCustomer(rowdata.userid) == -1)
                return false;
            return true;
        }
        function f_onCheckRow(checked, data)
        {
            if (checked) addCheckedCustomer(data.userid);
            else removeCheckedCustomer(data.userid);
        }
        function f_getChecked()
        {
            alert(checkedCustomer.join(','));
        }
		
		
		//表单搜索
		 function initSerchForm() {
            //$('#title').ligerComboBox({ width: 97, emptyText: '（空）'});         
            
        }
		
		function serchpanel() {
            initSerchForm();
            if ($(".az").css("display") == "none") {
                $("#grid").css("margin-top", $(".az").height() + "px");
                //$("#maingrid4").ligerGetGridManager().onResize();
                //$("#maingrid5").ligerGetGridManager().onResize();
				$(".az").css("display","inline");
				$(".az").css("position","absolute");
				$(".az").css("left","5px");
				$(".az").css("top","30px");
				
				//alert("1");
            } else {
                $("#grid").css("margin-top", "0px");
				$(".az").css("display","none");
				
                //$("#maingrid4").ligerGetGridManager().onResize();
                //$("#maingrid5").ligerGetGridManager().onResize();
				//alert("2");
            }
            $("#company").focus();
        }		
		//serchpanel();
    </script>

<body style="margin-top:0px">
 <div id="message" style="width:800px"></div>
<div class="l-loading" style="display:block" id="pageloading"></div> 
    <div id="toolbar" ></div>	
	<div id="grid">
		<div id="maingrid4" style="margin:-1px"></div>
		<!--<div id="toolbar1"></div>-->		
	</div>
  <div class="az">
        <form id='serchform'>
            <table style='width: 960px' class="bodytable1">
                <tr>
                    <td style='width:200px'>
                        <div style='float: left; text-align: right; width: 60px;'>制造商：</div>
						<div style='float: left;'>
						<input type='text' id='company_name' name='company_name' ltype='text' ligerui='{width:120}' />
						</div>
                    </td>					
                    <td style='align:left'>
                        <input  id='Button2' type='button' value='重置' style='height: 24px; width: 80px;'
                            onclick=" doclear() " />						
                        <input  id='Button1' type='button' value='搜索' style='height: 24px; width: 80px;' onclick=" doserch() " />
                    </td>
                </tr>
            </table>
        </form>
    </div>

  <div style="display:none;">
  
</div>

</body>
</html>
