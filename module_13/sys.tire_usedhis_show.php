﻿<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>轮胎装车总时查询</title>
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
<script src="../lib/ligerUI/js/plugins/ligerDateEditor.js" type="text/javascript"></script>
<script src="../lib/ligerUI/js/plugins/ligerPopupEdit.js" type="text/javascript"></script>
<script src="../lib/ligerUI/js/plugins/ligerComboBox.js" type="text/javascript"></script>

<script src="../js/XHD.js" type="text/javascript"> </script>
<script src="../lib/jquery.form.js" type="text/javascript"> </script>
</head>

 <script type="text/javascript">
	var manager;
	var menu;
	
	 function trim(str){ //删除左右两端的空格
　　     return str.replace(/(^\s*)|(\s*$)/g, "");
　　 }
　　 function ltrim(str){ //删除左边的空格
　　     return str.replace(/(^\s*)/g,"");
　　 }
　　 function rtrim(str){ //删除右边的空格
　　     return str.replace(/(\s*$)/g,"");
　　 }

 		      //工具条事件
		function itemclick(item) {
			switch (item.id) {
				case "searchbtn":
					serchpanel();
                  break;
 
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
			$('.pcontrol input', manager.toolbar).val(1);
			manager.changePage('input');
			manager.set({url:'../ajaction/v1/?menuid=131310&cmd=qry&'+serchtxt});
			//131110
			//manager.loadData(true);
        }
		/*
		function toolbar() {
				//这里需要改成根据用户权限来获取
                var toolbarOptions = {
          				items: [
            		{ text:'增加',id:'add', click:itemclick,img:"../lib/ligerUI/skins/icons/add.gif" },
            		{ line:true},
					{ text:'编缉',id:'edit',click:itemclick,img:"../lib/ligerUI/skins/icons/edit.gif" },
            		{ line:true},            		
					{ text:'查询',id:'searchbtn',click:itemclick,img:"../lib/ligerUI/skins/icons/search.gif" },
					]};
				$("#toolbar").ligerToolBar({items: toolbarOptions.items});
				menu = $.ligerMenu({ width: 120, items:toolbarOptions.items});         

           }
 		*/
        $(function () {
				
				  
                //$("#grid").height(document.documentElement.clientHeight - $(".toolbar").height());
				$('form').ligerForm();
				//toolbar();
				//
				serchpanel();
			/*
			时间
车辆编号
轮胎号位
车载控制器编号
轮胎胎号
轮胎压力
压力阀值
轮胎温度
温度上限
			*/	  
            
            manager=$("#maingrid4").ligerGrid({
                checkbox: false,
                columns: [{display: '安装时间', name: 'time_add',width:'20%'},
				{display: '卸载时间', name: 'time_remove',width:'20%'},
				{display: '车辆号码', name: 'plate_no',width:'10%'},
				{display: '轮胎号位', name: 'place_no',width:'10%',render:function(row,rowindex,value,column){
				
						return gettirename(Number(row.place_no));
						
					}
				},
				{display: '轮胎胎号', name: 'tire_no',width:'20%'},				
				{display: '使用时间', name: 'used_time',width:'20%',
				totalSummary:
                    {
                        type: 'sum',
						render: function (e) { 
							//汇总渲染器，返回html加载到单元格 
							//e 汇总Object(包括sum,max,min,avg,count) 
							var t=e.sum/60/60;
							return "<div>总数：" + t.toFixed(2) + "小时</div>"; 
						} 
                    }
				},
				], pageSize:10,
                /*url:'../ajaction/v1/?menuid=131010&cmd=qry&t=1',*/
				/*url:'../ajaction/sysaction/sys.roles_grid_show.php?a=3',*/
                /*toolbar:toolbarOptions,*/
                width: '100%',height:'97%',
				//dataAction: 'server', //服务器排序
                usePager: false,       //不分页
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
		function getGridOptions(checkbox) {
             var options = {
                 columns: [
                 { display: '胎号', name:'factory_code', width:150,isSort:false },
				{ display: '传感器编号', name:'sensor_no', width: 150},
				{ display: '规格',name:'norms_name',width: 120},
				{ display: '层级',name:'class_name',width: 100},
				{ display: '花纹',name:'figure_name',width: 100},
				{ display: '品牌',name:'brand_name',width: 100},								 
                 ], switchPageSizeApplyComboBox: false,
                 /*data: $.extend({}, CustomersData),*/
				 url:'../ajaction/v1/?menuid=111110&cmd=qry&t=1',
                 pageSize: 10,
				 dataAction:'server',
				 allowUnSelectRow:true
				
             };
             return options;
         }

		
		//表单搜索
		 function initSerchForm() {
            //$('#title').ligerComboBox({ width: 97, emptyText: '（空）'});         
 			 $("#tire_id").ligerPopupEdit({
             condition: {
                 prefixID:'factory_code',
                 fields: [{name:'factory_code',type:'text',label:'轮胎编码'},
						 {name:'sensor_no',type:'text',label:'传感器编码'}],
				 labelWidth:100
				 
             },
             grid: getGridOptions(true),
				valueField: 'tire_id',
				textField: 'factory_code',
				width: 150,
				searchClick:function(obj){
					/*
						obj 
						grid: grid,
                        rules: rules   {"op":"like","field":"plate_no","value":"苏1","type":"text"}
					*/
					var s="";
					for(var i=0;i<obj.rules.length;i++){
						s=s+obj.rules[i].field+"="+encodeURI(obj.rules[i].value);
					}
					if(s!="") s="&"+s;
					$('.pcontrol input', obj.grid.toolbar).val(1);
					obj.grid.changePage('input');
					obj.grid.set({url:'../ajaction/v1/?menuid=111110&cmd=qry&t=1'+s});
					obj.grid.loadData(true);
					
				},
				dlgWidth:800,
				dlgHeight:300,
				onSelect:function(x){
					//按了选择按钮
					/*
					value: value.join(p.split),
                text: text.join(p.split),
                data: data,
					*/
					
					/*if(x.data.length==1){
						//alert(x.data.length);
						//alert(x.data[0].wheel_count);  //选择了这个ID ,这个车子轮数
						var data=[{'id':0,'name':'所有'}];
						for(var i=1;i<=x.data[0].wheel_count;i++){
							data.push({'id':i,'name':i});
						}
						com_place.setData(data);
						com_place.selectValue(0);  //默认选中0
					}*/
					
				}
			});
			
			
			

		
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
				$(".az").css("top","0px");
				
				//alert("1");
            } else {
                $("#grid").css("margin-top", "0px");
				$(".az").css("display","none");
				
                //$("#maingrid4").ligerGetGridManager().onResize();
                //$("#maingrid5").ligerGetGridManager().onResize();
				//alert("2");
            }
            //$("#company").focus();
        }		
		//serchpanel();
    </script>
<style type="text/css">
#Button1{
			border: 1px solid #1c66dc;
			height: 25px;
			line-height: 2px;
			width: 100px;
			background-color: white;
			border-radius: 20px;
			vertical-align: middle;
			color: #1c66dc;
		}
</style>
<body style="margin-top:0px">
 <div id="message" style="width:800px"></div>
<div class="l-loading" style="display:none" id="pageloading"></div> 
  <form id="form1" onsubmit=" return false ">
    <div id="toolbar" ></div>	
	<div id="grid">
		<div id="maingrid4" style="margin:0px"></div>
		<!--<div id="toolbar1"></div>-->		
	</div>
  </form>
  <div class="az" style="display:none;">
        <form id='serchform' style="width:100%">
            <table style='width:100%' class="bodytable1">
                <tr>
                    <td style="width:70px">
                        <div style='float: left; text-align: right;margin-top: 10px;'>轮胎胎号：</div>
					</td>
					<td style="width:200px">
						<div style='float: left;'>
						<!--<input type='text' id='plate_no' name='plate_no'  ligerui='{width:120}' />-->
						<input id="tire_id"  type="text" readonly="" style="width: 120px;"   />
						</div>
                    </td>
					<td style='padding-top;10px;'>
                        						
                        <input  id='Button1' type='button' value='搜索' style='height: 24px; width: 80px;border-color:#1c66dc;background-color:#ffffff;border-radius:20px;' onclick=" doserch() " />
                    </td>
                </tr>
            </table>
        </form>
    </div>

  <div style="display:none;">
  
</div>

</body>
</html>
