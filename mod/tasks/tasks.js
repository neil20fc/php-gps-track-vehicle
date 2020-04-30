//#################################################
// VARS
//#################################################

var tasksData = new Array();
tasksData['imei'] = false;
tasksData['tasks'] = new Array();
tasksData['selected_id'] = false;

// timers
var timer_tasksLoadData;

//#################################################
// END VARS
//#################################################

function load()
{
        tasksLoadData();
}

function tasksLoadData()
{
	var data = {
		cmd: 'load_tasks_data',
		imei: tasksData['imei']
	};
	function objectSize(obj) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) size++;
		}
		return size;
	}
	$.ajax({
		type: "POST",
		url: "fn_tasks.php",
		data: data,
		dataType: 'json',
		error: function(statusCode, errorThrown) {
			timer_tasksLoadData = setTimeout("tasksLoadData();", 60 * 1000);
		},
		success: function(result)
		{
			tasksData['tasks'] = result;
			
                        tasksAddItems(result);
			
			timer_tasksLoadData = setTimeout("tasksLoadData();", 60 * 1000);
		}
	});
}

function tasksAddItems(items)
{
	var tasks_new_html = '';
	var tasks_in_progress_html = '';
	var tasks_completed_html = '';
	var tasks_failed_html = '';
	
	var tasks_new_cnt = 0;
	var tasks_in_progress_cnt = 0;
	var tasks_completed_cnt = 0;
	var tasks_failed_cnt = 0;
	
	for (var key in items)
        {
		var item = items[key];
		
		var priorityClass = '';
		if (item.status == 0 || item.status == 1) {
			priorityClass = " priority-"+item.priority;
		}
		
		var item_html = '<a href="#" onclick="tasksOpenItem('+item.task_id+');">';
                item_html += '<div class="task-container' +priorityClass +'">';
		item_html += '<ul class="list">';
		item_html += '<li class="row task-name">';
                item_html += '<div class="icon icon-tasks"></div>';
                item_html += '<div class="row-data">'+item.name+'</div>';
                item_html += '</li>';
		item_html += '<li class="row task-priority">';
                item_html += '<div class="icon icon-priority"></div>';
                item_html += '<div class="row-data">'+item.priority+'</div>';
                item_html += '</li>';
                item_html += '<li class="row task-start-address">';
		item_html += '<div class="icon icon-marker"></div>';
                item_html += '<div class="row-data">'+item.start_address+'</div>';
                item_html += '</li>';
                item_html += '<li class="row">';
		item_html += '<div class="icon icon-time"></div>';
                item_html += '<div class="row-data">'+item.start_from_dt+'</div>';
                item_html += '</li>';
		item_html += '</ul>';
                item_html += '</div>';
                item_html += '</a><div class="task-spacer"></div>';
		
		if (item.status == 0)
		{
			tasks_new_html += item_html;
			tasks_new_cnt += 1;
                }
		else if (item.status == 1)
		{
			tasks_in_progress_html += item_html;
			tasks_in_progress_cnt += 1;
                }
		else if (item.status == 2)
		{
			tasks_completed_html += item_html;
			tasks_completed_cnt += 1;
                }
		else if (item.status == 3)
		{
			tasks_failed_html += item_html;
			tasks_failed_cnt += 1;
                }
        }
		
	var task_list_html = '';
	
	if (tasks_new_html != '')
	{
		task_list_html += '<div class="tasks-title new"><span>New (' +tasks_new_cnt +')</div></div>'+tasks_new_html;
        }
	
	if (tasks_in_progress_html != '')
	{
		task_list_html += '<div class="tasks-title in-progress"><span>In progress (' +tasks_in_progress_cnt +')</div></div>'+tasks_in_progress_html;
        }
	
	if (tasks_completed_html != '')
	{
		task_list_html += '<div class="tasks-title completed"><span>Completed (' +tasks_completed_cnt +')</div></div>'+tasks_completed_html;
        }
	
	if (tasks_failed_html != '')
	{
		task_list_html += '<div class="tasks-title failed"><span>Failed (' +tasks_failed_cnt +')</div></div>'+tasks_failed_html;		
        }
	   
        document.getElementById("task_list").innerHTML = task_list_html;
		
}

function tasksOpenItem(id)
{
	tasksData['selected_id'] = id;
	
	if (tasksData['tasks'][id].status == 0)
	{
		document.getElementById('task_details_controls_confirm').style.display = '';
		document.getElementById('task_details_controls_completed').style.display = 'none';
	}
	else if (tasksData['tasks'][id].status == 1)
	{
		document.getElementById('task_details_controls_confirm').style.display = 'none';
		document.getElementById('task_details_controls_completed').style.display = '';
	}
	else
	{
		document.getElementById('task_details_controls_confirm').style.display = 'none';
		document.getElementById('task_details_controls_completed').style.display = 'none';
	}
	
	document.getElementById('task_list').style.display = 'none';
	document.getElementById('task_details').style.display = '';
	
	var task_status = '';
	if (tasksData['tasks'][id].status == 0) {
		task_status = ' new-task';
	} else if (tasksData['tasks'][id].status == 1) {
		task_status = ' in-progress';
	} else if (tasksData['tasks'][id].status == 2) {
		task_status = ' completed';
	} else if (tasksData['tasks'][id].status == 3) {
		task_status = ' failed';
	}
	
	var content_html = '<div class="task-details-content'+task_status+'">';
	content_html += '<div class="tasks-title details"><span>Details</span></div>';
	content_html += '<div class="task-container">';
	content_html += '<ul class="list">';
	content_html += '<li class="row task-name">';
	content_html += '<div class="icon icon-task"></div>';
	content_html += '<div class="row-data">'+tasksData['tasks'][id].name+'</div>';
	content_html += '</li>';
	
	if (tasksData['tasks'][id].desc != '')
	{
		content_html += '<li class="row task-desc">';
		content_html += '<div class="row-data">'+tasksData['tasks'][id].desc+'</div>';
		content_html += '</li>';
        }
	
	content_html += '<li class="row task-priority">';
	content_html += '<div class="icon icon-priority"></div>';
	content_html += '<div class="row-data">'+tasksData['tasks'][id].priority+'</div>';
	content_html += '</li>';
	content_html += '</ul>';
	content_html += '</div>';
	content_html += '<div class="task-spacer"></div>';
	
	content_html += '<div class="tasks-title blank task-start-title"><span>Start</span></div>';
	content_html += '<div class="task-container">';
	content_html += '<ul class="list">';
	content_html += '<li class="row task-start-address">';
	content_html += '<div class="icon icon-marker"></div>';
	content_html += '<div class="row-data"><strong>Address:</strong> '+tasksData['tasks'][id].start_address+'</div>';
	content_html += '</li>';
	content_html += '<li class="row task-start-dt">';
	content_html += '<div class="icon icon-time"></div>';
	content_html += '<div class="row-data"><strong>Start (time window):</strong> from '+tasksData['tasks'][id].start_from_dt+' till '+tasksData['tasks'][id].start_to_dt+'</div>';
	content_html += '</div>';
	content_html += '<div class="task-spacer"></div>';
	
	content_html += '<div class="tasks-title blank task-destination-title"><span>Destination</span></div>';
	content_html += '<div class="task-container">';
	content_html += '<ul class="list">';
	content_html += '</li>';
	content_html += '<li class="row task-end-address">';
	content_html += '<div class="icon icon-marker"></div>';
	content_html += '<div class="row-data"><strong>Address:</strong> '+tasksData['tasks'][id].end_address+'</div>';
	content_html += '</li>';
	content_html += '<li class="row task-end-dt">';
	content_html += '<div class="icon icon-time"></div>';
	content_html += '<div class="row-data"><strong>End (time window):</strong> from '+tasksData['tasks'][id].end_from_dt+' till '+tasksData['tasks'][id].end_to_dt+'</div>';
	content_html += '</li>';
	content_html += '</ul>';
	content_html += '<div>';
	content_html += '<div>';
	
	document.getElementById('task_details_content').innerHTML = content_html;	
}

function tasksCloseItem()
{
	tasksData['selected_id'] = false;
	
        document.getElementById('task_list').style.display = '';
	document.getElementById('task_details').style.display = 'none';
	
	document.getElementById('task_details_content').innerHTML = '';
}

function tasksCancel()
{
	if (tasksData['selected_id']) {
		var data = {
			cmd: 'cancel_task',
			imei: tasksData['imei'],
			id: tasksData['selected_id']
		};
		
		$.ajax({
			type: "POST",
			url: "fn_tasks.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					tasksLoadData();
					tasksCloseItem();
				}
				
			}
		});
        }
}

function tasksConfirm()
{
	if (tasksData['selected_id']) {
		var data = {
			cmd: 'confirm_task',
			imei: tasksData['imei'],
			id: tasksData['selected_id']
		};
		
		$.ajax({
			type: "POST",
			url: "fn_tasks.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					tasksLoadData();
					tasksCloseItem();
				}
			}
		});
        }
}

function tasksComplete()
{
	if (tasksData['selected_id']) {
		var data = {
			cmd: 'complete_task',
			imei: tasksData['imei'],
			id: tasksData['selected_id']
		};
		
		$.ajax({
			type: "POST",
			url: "fn_tasks.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					tasksLoadData();
					tasksCloseItem();
				}
			}
		});
        }
}