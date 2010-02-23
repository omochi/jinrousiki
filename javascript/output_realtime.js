function output_realtime(){
  var seconds, time, value;
  var left_seconds = Math.floor((end_date - new Date()) / 1000);

  if(left_seconds > 0){
    var left_time = new Date(0, 0, 0, 0, 0, left_seconds);
    seconds = Math.floor(12 * 60 * 60 * left_seconds / diff_seconds);
    time = new Date(0, 0, 0, 0, 0, seconds);
    value = sentence;
    if(time.getHours()   > 0){value += time.getHours()   + "����";}
    if(time.getMinutes() > 0){value += time.getMinutes() + "ʬ";}
    value += "(�»��� ";
    if(left_time.getHours()   > 0){value += left_time.getHours()   + "����";}
    if(left_time.getMinutes() > 0){value += left_time.getMinutes() + "ʬ";}
    if(left_time.getSeconds() > 0){value += left_time.getSeconds() + "��";}
    value += ")";
  }
  else{
    time = new Date(0, 0, 0, 0, 0, Math.abs(left_seconds));
    value = "Ķ����� ";
    if(time.getHours()   > 0){value += time.getHours()   + "����";}
    if(time.getMinutes() > 0){value += time.getMinutes() + "ʬ";}
    if(time.getSeconds() > 0){value += time.getSeconds() + "��";}
  }
  document.realtime_form.output_realtime.value = value;
  tid = setTimeout("output_realtime()", 1000);
}
