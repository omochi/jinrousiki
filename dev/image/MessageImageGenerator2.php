<?php

/*
  MessageImageGenerator.php
  Ver. 1.0 作成
  Ver. 1.1 #の処理を追加
 */

class MessageImageGenerator {
  var $font;   // フォントパス
  var $size;   // フォントサイズ
  var $width;  // 半角1文字あたりの幅
  var $height; // 半角1文字あたりの高さ
  var $x_margin; // マージン幅
  var $y_margin; // マージン高さ
  var $is_trans; // 背景色を透明にするかどうか

  /*
    コンストラクタ
    $font_ 使用するTrueTypeフォントのパス
    $size_ フォントサイズ
    $x_margin_ マージン幅
    $y_margin_ マージン高さ
  */
  function MessageImageGenerator($font_ = "C:\\WINDOWS\\Fonts\\msgothic.ttc", $size_ = 12, $x_margin_ = 5, $y_margin_ = 2, $is_trans_ = false) {
    $this->font = $font_;
    $this->size = $size_;
    $this->x_margin = $x_margin_;
    $this->y_margin = $y_margin_;
    $this->is_trans = $is_trans_;

    // フォント幅・高さの測定。もっといい定跡があればそちらに変更する予定。
    $r_a = imagettfbbox($this->size, 0, $this->font, "A");
    $r_a2 = imagettfbbox($this->size, 0, $this->font, "AA");
    $r_a2v = imagettfbbox($this->size, 0, $this->font, "A\nA");
    $this->width = $r_a2[2] - $r_a[2];
    $this->height = $r_a2v[1] - $r_a[1];
  }

  /*
    役職説明、能力実行結果などのメッセージ用画像ファイルを生成する関数
    $msg 作成したいメッセージ文。改行有効。||で囲んだ部分を指定した色で書く
    $cr,$cb,$cg 強調部分の表示に使用する色
    返り値 画像データ
  */
  function GetImage($msg, $cr = 0, $cg = 0, $cb = 0, $cr2 = 0, $cg2 = 0, $cb2 = 0,
		    $cr3 = 0, $cg3 = 0, $cb3 = 0, $cr4 = 0, $cg4 = 0, $cb4 = 0) {
    //$plain_msg_len = strlen($plain_msg);
    //echo "plain_r: $plain_msg_len ";
    $plain_msg = mb_convert_encoding(preg_replace('/[\|#_^]/', '', $msg), "UTF-8", "auto");
    $plain_r = imagettfbbox($this->size, 0, $this->font, $plain_msg);
    //print_r($plain_r);
    //echo "<br>";

    // 画像の生成
    $img = imagecreatetruecolor($plain_r[2] - $plain_r[6] + $this->x_margin * 2, $plain_r[3] - $plain_r[7] + $this->y_margin * 2);
    $col_black = imagecolorallocate($img, 0, 0, 0);
    $col_white = imagecolorallocate($img, 255, 255, 255);
    $col_appeal = imagecolorallocate($img, $cr, $cg, $cb);
    $col_appeal2 = imagecolorallocate($img, $cr2, $cg2, $cb2);
    $col_appeal3 = imagecolorallocate($img, $cr3, $cg3, $cb3);
    $col_appeal4 = imagecolorallocate($img, $cr4, $cg4, $cb4);
    // 背景を透明色に設定する場合
    if ($this->is_trans) imagecolortransparent($img, $col_white);
    imagefill($img, 0, 0, $col_white);

    // 各行ごとに処理
    $msg_lines = preg_split('/\n/', $msg, -1, PREG_SPLIT_NO_EMPTY);
    $y_disp = $this->y_margin;
    $col_flag = false;
    foreach ($msg_lines as $line) {
      // この行でどれだけ消費するか計算
      $line_len = strlen($line);
      $line_plain = mb_convert_encoding(preg_replace('/[\|#_^]/', '', $line), "UTF-8", "auto");
      $r = imagettfbbox($this->size, 0, $this->font, $line_plain);
      //echo "line_r: $line_len ";
      //print_r($r);
      //echo "<br>";

      // 強調部分の色を変えつつ表示
      $array_msg = preg_split('/[\|#_^]/', $line, -1, PREG_SPLIT_OFFSET_CAPTURE);
      //$x_disp = $this->x_margin;
      $str_total = "";
      for ($i = 0 ; $i < count($array_msg) ; $i++) {
	$str_len = strlen($array_msg[$i][0]);
	//echo "str_r: $str_len ";
	$str = mb_convert_encoding($array_msg[$i][0], "UTF-8", "auto");
	$str_total .= $str;
	$r_str = imagettfbbox($this->size, 0, $this->font, $str);
	$r_str_total = imagettfbbox($this->size, 0, $this->font, $str_total);
	//print_r($r_str);
	//echo "<br>";

	// 文字色の決定
	$color = $col_black;
	if ($array_msg[$i][1] > 0 && $array_msg[$i][1] + $str_len < $line_len) {
	  if ($line[$array_msg[$i][1] - 1] == '|' && $line[$array_msg[$i][1] + $str_len] == '|') $color = $col_appeal;
	  elseif ($line[$array_msg[$i][1] - 1] == '#' && $line[$array_msg[$i][1] + $str_len] == '#') $color = $col_appeal2;
	  elseif ($line[$array_msg[$i][1] - 1] == '_' && $line[$array_msg[$i][1] + $str_len] == '_') $color = $col_appeal3;
	  elseif ($line[$array_msg[$i][1] - 1] == '^' && $line[$array_msg[$i][1] + $str_len] == '^') $color = $col_appeal4;
	}
	
	// 文字列の描画
	imagettftext($img, $this->size, 0, $this->x_margin + $r_str_total[2] - $r_str[2], 0 - $r[5] + $y_disp, $color, $this->font, $str);
	// Boldにするときは下の行も実行
//	imagettftext($img, $this->size, 0, $this->x_margin + $r_str_total[2] - $r_str[2] + 1, 0 - $r[5] + $y_disp, $color, $this->font, $str);

//	$x_disp += $str_len * $this->width + $r[0];
	$col_flag = !$col_flag;
      }
      $y_disp += $this->height;
      $col_flag = false;
    }

    return $img;
  }
}

/* サンプルとして紹介されていたBold出力関数。濃すぎるので没。
function drawboldtext($image, $size, $angle, $x_cord, $y_cord, $color, $fontfile, $text)
{
   $_x = array(1, 0, 1, 0, -1, -1, 1, 0, -1);
   $_y = array(0, -1, -1, 0, 0, -1, 1, 1, 1);
   for($n=0;$n<=8;$n++)
   {
     ImageTTFText($image, $size, $angle, $x_cord+$_x[$n], $y_cord+$_y[$n], $color, $fontfile, $text);
   }
}*/
/*
header("Content-Type: image/png");

$gen = new MessageImageGenerator("C:\\WINDOWS\\Fonts\\uzura.ttf", 20, 5, 2, false);

$image = $gen->GetImage("[役割]\n　#あなたは#|人狼|#です#。夜の間に他の人狼と協力し村人一人を殺害できます。" .
		"あなたはその強力な力で村人を喰い殺すのです！",
		255, 0, 0,
		0, 0, 255);

$image = $gen->GetImage("t|e|s#t#", 255, 0, 0, 0, 0, 255);
//imagegif($image, "c:\\temp\\result.gif"); // ファイルに出力する場合
imagegif($image);
*/
?>
