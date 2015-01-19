<?php
/* 
 *  .oPYo.                      o      o                 o  8                   .oPYo.                                   
 *  8   `8                      8      8                 8  8                   8                                        
 * o8YooP' .oPYo. .oPYo. .oPYo. 8      8 .oPYo. .oPYo.  o8P 8oPYo. .oPYo. oPYo. `Yooo. odYo. .oPYo. .oPYo. .oooo. .oPYo. 
 *  8   `b .oooo8 Yb..   8    8 8  db  8 8oooo8 .oooo8   8  8    8 8oooo8 8  `'     `8 8' `8 8    8 8    8   .dP  8oooo8 
 *  8    8 8    8   'Yb. 8    8 `b.PY.d' 8.     8    8   8  8    8 8.     8          8 8   8 8    8 8    8  oP'   8.     
 *  8    8 `YooP8 `YooP' 8YooP'  `8  8'  `Yooo' `YooP8   8  8    8 `Yooo' 8     `YooP' 8   8 `YooP' `YooP' `Yooo' `Yooo' 
 * :..:::..:.....::.....:8 ....:::..:..:::.....::.....:::..:..:::..:.....:..:::::.....:..::..:.....::.....::.....::.....:
 * ::::::::::::::::::::::8 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 * ::::::::::::::::::::::..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 */

        //Modified original version found at http://pastebin.com/1MYJVw4W
        //from https://github.com/dawithers/pisysinfo
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Pragma: no-cache");

	function NumberWithCommas($in)
	{
		return number_format($in);
	}
	function  WriteToStdOut($text)
	{
		$stdout = fopen('php://stdout','w') or die($php_errormsg);
		fputs($stdout, "\n" . $text);
	}
	
	$current_time = date('d M Y') . '<br />' . date('h:i:s T');
	$frequency = NumberWithCommas(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000);
	$processor = str_replace("-compatible processor", "", explode(": ", chop(file("/proc/cpuinfo")[0]))[1]);
	$cpu_temperature = round(file_get_contents("/sys/class/thermal/thermal_zone0/temp") / 1000, 1);
	//$RX = exec("ifconfig eth0 | grep 'RX bytes'| cut -d: -f2 | cut -d' ' -f1");
	//$TX = exec("ifconfig eth0 | grep 'TX bytes'| cut -d: -f3 | cut -d' ' -f1");
	$system = chop(file_get_contents("/proc/sys/kernel/ostype"));
	$host = chop(file_get_contents("/proc/sys/kernel/hostname"));
	$kernel = chop(file_get_contents("/proc/sys/kernel/osrelease"));
	
	//Uptime
	$uptime_array = explode(" ", file_get_contents("/proc/uptime"));
	$seconds = round($uptime_array[0], 0);
	$minutes = $seconds / 60;
	$hours = $minutes / 60;
	$days = floor($hours / 24);
	$hours = sprintf('%02d', floor($hours - ($days * 24)));
	$minutes = sprintf('%02d', floor($minutes - ($days * 24 * 60) - ($hours * 60)));
	if ($days == 0):
		$uptime = $hours . ":" .  $minutes . " (hh:mm)";
	elseif($days == 1):
		$uptime = $days . " day, " .  $hours . ":" .  $minutes . " (hh:mm)";
	else:
		$uptime = $days . " days, " .  $hours . ":" .  $minutes . " (hh:mm)";
	endif;
	
	//CPU Usage
	$output1 = null;
	$output2 = null;
	//First sample
	$output1 = file("/proc/stat");
	//Sleep before second sample
	sleep(1);
	//Second sample
	$output2 = file("/proc/stat");
	$cpuload = 0;
	for ($i=0; $i < 1; $i++)
	{
		//First row
		$cpu_stat_1 = explode(" ", $output1[$i+1]);
		$cpu_stat_2 = explode(" ", $output2[$i+1]);
		//Init arrays
		$info1 = array("user"=>$cpu_stat_1[1], "nice"=>$cpu_stat_1[2], "system"=>$cpu_stat_1[3], "idle"=>$cpu_stat_1[4]);
		$info2 = array("user"=>$cpu_stat_2[1], "nice"=>$cpu_stat_2[2], "system"=>$cpu_stat_2[3], "idle"=>$cpu_stat_2[4]);
		$idlesum = $info2["idle"] - $info1["idle"] + $info2["system"] - $info1["system"];
		$sum1 = array_sum($info1);
		$sum2 = array_sum($info2);
		//Calculate the cpu usage as a percent
		$load = (1 - ($idlesum / ($sum2 - $sum1))) * 100;
		$cpuload += $load;
	}
	$cpuload = round($cpuload, 1); //One decimal place
	
	//Memory Utilisation
	$meminfo = file("/proc/meminfo");
	for ($i = 0; $i < count($meminfo); $i++)
	{
		list($item, $data) = split(":", $meminfo[$i], 2);
		$item = trim(chop($item));
		$data = intval(preg_replace("/[^0-9]/", "", trim(chop($data)))); //Remove non numeric characters
		switch($item)
		{
			case "MemTotal": $total_mem = $data; break;
			case "MemFree": $free_mem = $data; break;
			case "SwapTotal": $total_swap = $data; break;
			case "SwapFree": $free_swap = $data; break;
			case "Buffers": $buffer_mem = $data; break;
			case "Cached": $cache_mem = $data; break;
			default: break;
		}
	}
	$used_mem = $total_mem - $free_mem;
	$used_swap = $total_swap - $free_swap;
	$percent_free = round(($free_mem / $total_mem) * 100);
	$percent_used = round(($used_mem / $total_mem) * 100);
	$percent_swap = round((($total_swap - $free_swap ) / $total_swap) * 100);
	$percent_swap_free = round(($free_swap / $total_swap) * 100);
	$percent_buff = round(($buffer_mem / $total_mem) * 100);
	$percent_cach = round(($cache_mem / $total_mem) * 100);
	$used_mem = NumberWithCommas($used_mem);
	$used_swap = NumberWithCommas($used_swap);
	$total_mem = NumberWithCommas($total_mem);
	$free_mem = NumberWithCommas($free_mem);
	$total_swap = NumberWithCommas($total_swap);
	$free_swap = NumberWithCommas($free_swap);
	$buffer_mem = NumberWithCommas($buffer_mem);
	$cache_mem = NumberWithCommas($cache_mem);

	//Disk space check
	$lines = file("/proc/mounts");

	foreach($lines as $line) {
		if (preg_match('/tmpfs|devtmpfs|devpts|rootfs|proc|sysfs/', $line) == false) {
			list($drive[], $mount[], $typex[]) = explode(" ", $line);
		}
	}

	foreach($mount as $dir) {
		$avail[] = disk_free_space($dir) / 1048576;
		$size[] = disk_total_space($dir) / 1048576;
		$used[] = (disk_total_space($dir) - disk_free_space($dir)) / 1048576;
		$percent[] = round((1 - (disk_free_space($dir) / disk_total_space($dir)))*100);
	}
        
        
        $info = new stdClass();
        $info->host = $host;
        $info->kernel = $system . ' ' . $kernel;
        $info->processor = $processor;
        $info->frequency = $frequency;
        $info->cpuload = $cpuload;
        $info->uptime = $uptime;
        $info->cpu_temperature = $cpu_temperature;
        $info->total_mem = $total_mem;
        $info->used_mem = $used_mem;
        $info->percent_used = $percent_used;
        $info->free_mem = $free_mem;
        $info->percent_free = $percent_free;
        $info->buffer_mem = $buffer_mem;
        $info->percent_buff = $percent_buff;
        $info->cache_mem = $cache_mem;
        $info->percent_cach = $percent_cach;
        $info->total_swap = $total_swap;
        $info->used_swap = $used_swap;
        $info->percent_swap = $percent_swap;
        $info->percent_swap_free = $percent_swap_free;
        $info->mount_info = [];
        
        for ($i = 0; $i < count($drive); $i++)
	{
            $drive_info = new stdClass();
            $drive_info->total = number_format(round($size[$i])) . " MB";
            $drive_info->usedspace = number_format(round($used[$i])) . " MB";
            $drive_info->freespace = number_format(round($avail[$i])) . " MB";
            $drive_info->name = $mount[$i] . " (" . $typex[$i] . ")";
            $drive_info->percent_usedspace = $percent[$i];
            $drive_info->percent_freespace = (100-(floatval($percent[$i])));
            array_push($info->mount_info, $drive_info);
            
	}
        
        echo json_encode($info);