$.fn.gauge = function(opts) {
  this.each(function() {
    var $this = $(this),
        data = $this.data();

    if (data.gauge) {
      data.gauge.stop();
      delete data.gauge;
    }
    if (opts !== false) {
      data.gauge = new Gauge(this).setOptions(opts);
    }
  });
  return this;
};
//From http://stackoverflow.com/questions/946534/insert-text-into-textarea-with-jquery
jQuery.fn.extend({
insertAtCaret: function(myValue){
  return this.each(function(i) {
    if (document.selection) {
      //For browsers like Internet Explorer
      this.focus();
      var sel = document.selection.createRange();
      sel.text = myValue;
      this.focus();
    }
    else if (this.selectionStart || this.selectionStart == '0') {
      //For browsers like Firefox and Webkit based
      var startPos = this.selectionStart;
      var endPos = this.selectionEnd;
      var scrollTop = this.scrollTop;
      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
      this.focus();
      this.selectionStart = startPos + myValue.length;
      this.selectionEnd = startPos + myValue.length;
      this.scrollTop = scrollTop;
    } else {
      this.value += myValue;
      this.focus();
    }
  });
}
});

$(function() {
    
  var opts = {
    lines: 12, // The number of lines to draw
    angle: 0.14, // The length of each line
    lineWidth: 0.37, // The line thickness
    pointer: {
      length: 1, // The radius of the inner circle
      strokeWidth: 0.035, // The rotation offset
      color: '#000000' // Fill color
    },
    limitMax: 'false',   // If true, the pointer will not go past the end of the gauge
    colorStart: '#89CF9B',   // Colors
    colorStop: '#36DA1C',    // just experiment with them
    strokeColor: '#E0E0E0',   // to see which ones work best for you
    generateGradient: true,
    percentColors: [[0.0, "#a9d70b" ], [0.40, "#a9d70b"], [0.70, "#f9c802"], [1.0, "#ff0000"]]
  }; 
    $('#cpu_temperature_gauge').gauge(opts).data().gauge.maxValue=80;
    $('#cpu_temperature_gauge').data().gauge.set(0);
    
    $('#percent_used_gauge').gauge(opts).data().gauge.maxValue=100;
    $('#percent_used_gauge').data().gauge.set(0);
    $('#used_swap_gauge').gauge(opts).data().gauge.maxValue=100;
    $('#used_swap_gauge').data().gauge.set(0);
    $('#percent_buff_gauge').gauge(opts).data().gauge.maxValue=100;
    $('#percent_buff_gauge').data().gauge.set(0);
    
    $('#alarms-page').hide();
    $('#settings-page').hide();
    
    $('#btn-new-alarm').click(function(e) {
       e.preventDefault();
       $('#modal_alarm').modal('show');
    });
    
    $('#save-alarm').click(function(e) {
       e.preventDefault(); 
       $.ajax({
            type: "POST",
            url: 'api/index.php/alarm',
            dataType: 'json',
            data: {
                hour: $('#alarm-hour').data('DateTimePicker').getDate(),
                flag_weather: 1,
                tones: '',
                monday: $('#monday').prop('checked') || $('#all-days').prop('checked') ? 1 : 0,
                tuesday: $('#tuesday').prop('checked') || $('#all-days').prop('checked') ? 1 : 0,
                wednesday: $('#wednesday').prop('checked') || $('#all-days').prop('checked') ? 1 : 0,
                thursday: $('#thursday').prop('checked') || $('#all-days').prop('checked') ? 1 : 0,
                friday: $('#friday').prop('checked') || $('#all-days').prop('checked') ? 1 : 0,
                saturday: $('#saturday').prop('checked') || $('#all-days').prop('checked') ? 1 : 0,
                sunday: $('#sunday').prop('checked') || $('#all-days').prop('checked') ? 1 : 0,
            },
        }).done(function(json_response) {
            if(json_response.success) {
                $('#modal_alarm').modal('hide');
                readAlarms();
            }
        });
    });
    
    $('.navigation-link').click(function(e) {
       e.preventDefault();
       $('#dashboard-page').hide();
       $('#alarms-page').hide();
       $('#settings-page').hide();
       $('.navigation-link').each(function() {
           $(this).parent().removeClass('active');
       });
       $(this).parent().addClass('active');
       
       $($(this).attr('href')+'-page').fadeIn();
    });
    
    $('#btn-save-settings').click(function(e) {
       e.preventDefault(); 
        $.ajax({
            type: "PUT",
            url: 'api/index.php/settings/espeak_text',
            dataType: 'json',
            data: {
                value: $('#espeak-text').val()
            }
        }).done(function(json_response) {
            
        });
    });
    
    $('#alarm-hour').datetimepicker({
      pickDate: false,
      format: 'HH:mm',
    });
   
  function readSystemInfo() {  
  $.ajax({
            type: "GET",
            url: 'info_json.php',
            dataType: 'json',
        }).done(function(json_response) {
            //console.log(JSON.stringify(json_response));
            $('#host').html(json_response.host);
            $('#kernel').html(json_response.kernel);
            $('#frequency').html(json_response.frequency + ' Mhz');
            $('#uptime').html(json_response.uptime);
            $('#cpuload').html(json_response.cpuload + ' %');
            $('#total_mem').html(json_response.total_mem + ' KB');
            $('#used_mem').html(json_response.used_mem + ' KB');
            $('#cpu_temperature_gauge').data().gauge.set(json_response.cpu_temperature);
            $('#cpu_temperature_text').text(json_response.cpu_temperature+"°C");
            
            $('#percent_used_gauge').data().gauge.set(json_response.percent_used);
            $('#percent_used_text').text(json_response.percent_used+"%");
            
            $('#mount_info tbody').html('');
            for(var i=0; i<json_response.mount_info.length; i++) {
                var progress = '<div class="progress" style="height:15px;margin-bottom: initial;"><div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'+json_response.mount_info[i].percent_usedspace+'" aria-valuemin="0" aria-valuemax="100" style="width: '+json_response.mount_info[i].percent_usedspace+'%"><span class="sr-only">'+json_response.mount_info[i].percent_usedspace+'% Used</span></div></div>';
                $('#mount_info tbody').append('<tr><td>'+json_response.mount_info[i].name+'</td><td>'+json_response.mount_info[i].total+'</td><td>'+json_response.mount_info[i].usedspace+' ('+json_response.mount_info[i].percent_usedspace+'%)<br/>'+progress+'</td><td>'+json_response.mount_info[i].freespace+' ('+json_response.mount_info[i].percent_freespace+'%)</td></tr>');
            }
            
            $('#used_swap_gauge').data().gauge.set(json_response.used_swap);
            $('#used_swap_text').text(json_response.used_swap+"%");
            $('#percent_buff_gauge').data().gauge.set(json_response.percent_buff);
            $('#percent_buff_text').text(json_response.percent_buff+"%");
        });
    }   
        
    $('#btn_refresh_weather').click(function(e) {
       e.preventDefault();
       updateWeather();
    });
    
    $('#btn-update-system-info').click(function(e) {
       e.preventDefault();
       readSystemInfo();
    });
    
    $('.special_field').click(function(e) {
       e.preventDefault();
       $('#espeak-text').insertAtCaret($(this).attr('data-field'));
    });
    
    updateWeather();
    readAlarms();
    readSystemInfo();
    
    $.ajax({
            type: "GET",
            url: 'api/index.php/settings/espeak_text',
            dataType: 'json',
        }).done(function(json_response) {
            $('#espeak-text').val(json_response.value);
        });
    
    setInterval( function() {
        
        // Create a newDate() object and extract the seconds of the current time on the visitor's
	var seconds = new Date().getSeconds();
	// Add a leading zero to seconds value
	$("#sec").html(( seconds < 10 ? "0" : "" ) + seconds);
        
        var minutes = new Date().getMinutes();
	// Add a leading zero to the minutes value
	$("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
        
        var hours = new Date().getHours();
	// Add a leading zero to the hours value
	$("#hours").html(( hours < 10 ? "0" : "" ) + hours);
        
	},1000);
    
});

function readAlarms() {
    $('#table-alarms tbody').html('');
    $.ajax({
            type: "GET",
            url: 'api/index.php/alarm',
            dataType: 'json',
        }).done(function(json_response) {
            for(var i=0; i<json_response.length; i++) {
                $riga = $('<tr></tr>');
                $riga.append('<td style="font-size:22px; text-align:center;">'+json_response[i].hour+'</td>');
                $riga.append('<td style="line-height:26px; text-align:center;">'+(json_response[i].monday==="1" ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-muted"></span>') +'</td>');
                $riga.append('<td style="line-height:26px; text-align:center;">'+(json_response[i].tuesday==="1" ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-muted"></span>') +'</td>');
                $riga.append('<td style="line-height:26px; text-align:center;">'+(json_response[i].wednesday==="1" ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-muted"></span>') +'</td>');
                $riga.append('<td style="line-height:26px; text-align:center;">'+(json_response[i].thursday==="1" ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-muted"></span>') +'</td>');
                $riga.append('<td style="line-height:26px; text-align:center;">'+(json_response[i].friday==="1" ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-muted"></span>') +'</td>');
                $riga.append('<td style="line-height:26px; text-align:center;">'+(json_response[i].saturday==="1" ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-muted"></span>') +'</td>');
                $riga.append('<td style="line-height:26px; text-align:center;">'+(json_response[i].sunday==="1" ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-muted"></span>') +'</td>');
                $riga.append('<td><!--<a href="#" class="btn btn-xs btn-warning btn-edit-alarm" data-id="'+json_response[i].id+'"><span class="glyphicon glyphicon-edit"></span></a> --><a href="#" class="btn btn-xs btn-danger btn-delete-alarm" data-id="'+json_response[i].id+'"><span class="glyphicon glyphicon-trash"></span></a></td>')
                $('#table-alarms tbody').append($riga);
            }
            $('.btn-delete-alarm').click(function(e) {
               e.preventDefault();
               var id = $(this).attr('data-id');
               $.ajax({
                    type: "DELETE",
                    url: 'api/index.php/alarm/'+id,
                    dataType: 'json',
               }).done(function(json_response) {
                    readAlarms();
               });
            });
        });
}

function updateWeather() {    
    $.ajax({
            type: "GET",
            url: 'weather_json.php',
            dataType: 'json',
        }).done(function(json_response) {
            $('#wind_direction').addClass('_'+Math.abs(180-json_response.wind_direction_deg)+'-deg');
            $('#wind').html(json_response.wind_speed + ' km/h ('+json_response.wind_direction_deg+') ' + json_response.wind_direction);
            $('#sunset').html(json_response.sunset);
            $('#sunrise').html(json_response.sunrise);
            $('#current_temperature').html(json_response.temp + '<span class="wi wi-celsius"></span>');
            $('#current_condition_text').html(json_response.condition_original_text);
            $('#current_condition_icon').removeClass();
            $('#current_condition_icon').addClass('wi').addClass(json_response.forecast_icon);
            $('#forecast_table tbody').html('');
            for(var i=0; i<json_response.forecast.length; i++) {
                $('#forecast_table tbody').append('<tr><td>'+json_response.forecast[i].day+'</td><td class="text-center"><span class="wi '+json_response.forecast[i].icon+'"></span></td><td>'+json_response.forecast[i].text+'</td><td class="text-center">'+json_response.forecast[i].low+'<span class="wi wi-celsius"></span></td><td class="text-center">'+json_response.forecast[i].high+'<span class="wi wi-celsius"></span></td></tr>');
            }
        });
    }