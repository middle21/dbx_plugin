    function loadJS(url)
    {
        // adding the script tag to the head
       var head = document.getElementsByTagName('head')[0];
       var script = document.createElement('script');
       script.type = 'text/javascript';
       script.src = url;

       // fire the loading
       head.appendChild(script);
    }


function delete_popup(db,table,id,hostname,user,pass,primary){
	var r = confirm("Do you realy want to delete this record?");
	if(r)
	{
		var action = 1;
			$.ajax({
        			url: "dbx_plugin/src/dbx.handler.php",
				async: "true",
       			type: "post",
				dataType: "json",
        			data: "db_name="+db+"&table="+table+"&action="+action+"&host="+hostname+"&user="+user+"&pass="+pass,
				success: function(data){
					var arr = $.map(data , function(el){return el;});
					var arrlen = arr.length;
					var prim_arr = arr[arrlen-2].split("`");
					var primary = prim_arr[1];
						var a = 0;
						console.log("complete1");
						$.ajax({
        						url: "dbx_plugin/src/dbx.handler.php",
							async: "true",
       						type: "post",
							dataType : "html",
        						data: "db_name="+db+"&table="+table+"&id="+id+"&action="+a+"&hostname="+hostname+"&user="+user+"&pass="+pass+"&primary="+primary,
							success: function(data){
								console.log(data);
								console.log("complete2");
        						},
        						error: function(data,errorThrown){
								alert(data);
        						}
    						});	
        			},
        			error: function(data,errorThrown){
	    				alert("error");
        			}
    			});	

	}
}

function edit_mode_update(text,clsn,db,table,host,user,pass){

	var action = 1;
			$.ajax({
        			url: "dbx_plugin/src/dbx.handler.php",
				async: "true",
       			type: "post",
				dataType: "json",
        			data: "db_name="+db+"&table="+table+"&action="+action+"&host="+host+"&user="+user+"&pass="+pass,
				success: function(data){
					var arr = $.map(data , function(el){return el;});
					var o = clsn.className;
					var q = o.split("B");
					
					if(q[2].indexOf(" ") > -1)
					{ 
						var q2 = q[2].split(" ");
						q[2] = q2[0];
					}
					var arrlen = arr.length;
					var prim_arr = arr[arrlen-2].split("`");
					var primary = prim_arr[1];
					
					var record_id = q[1];
					var column = q[2];
					column = arr[3*column]
					var new_value = 0;

					if(arr[3*q[2]+2] || arr[3*q[2]+1] == primary)
					{
						console.log("can't modify that");
						//styled alert
					}else{
						console.log("go ahead");
						clsn.innerHTML = "<input type='text' id='" + clsn.className + "' value=" + text + ">";
						clsn.innerHTML += "<button onclick='save_edit(&quot;" + clsn.className + "&quot;,&quot;" + record_id + "&quot;,&quot;" + db + "&quot;,&quot;" + table + "&quot;,&quot;" + user + "&quot;,&quot;" + pass + "&quot;,&quot;" + host + "&quot;,&quot;" + column + "&quot;,&quot;" + new_value + "&quot;,&quot;" + primary + "&quot;);'>Save</button>";
					}
					
        			},
        			error: function(data,errorThrown){
	    				alert("error");
        			}
    			});
	
}

function save_edit(id_of_input,record_id,db,table,user,pass,host,column,new_value,primary){
	var obj = document.getElementById(id_of_input);
	new_value = obj.value;
	
	var action = 2;
			$.ajax({
        			url: "dbx_plugin/src/dbx.handler.php",
				async: "true",
       			type: "post",
				dataType: "text",
        			data: "db_name="+db+"&table="+table+"&action="+action+"&host="+host+"&user="+user+"&pass="+pass+"&record_id="+record_id+"&column="+column+"&new_value="+new_value+"&primary="+primary,
				success: function(data){
					var parent = obj.parentNode;
					parent.innerHTML = data;
					
        			},
        			error: function(data,errorThrown){
	    				alert(data);
        			}
    			});

}
