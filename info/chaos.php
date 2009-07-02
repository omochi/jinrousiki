<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>汝は人狼なりや？<?php echo $server_comment; ?></title>
</head>
<body>
<pre>
◆闇鍋村について
・配役決定ルーチン
※かなり複雑なので分からなかったらごめんなさい。
　調整中なので alpha 版はバージョンアップするたびに変わる可能性があります。

まず、各陣営ごとに出現人数枠を決定します
・通常闇鍋は人狼・妖狐・恋人陣営の総数は通常と同じです。
  (陣営内の内訳はシャッフルされます)

・人狼陣営
※一定数を確保します (人数が増えるごとにブレを大きくするのもありかな？)
8人未満：1:2 = 80:20 (80%で1人、20%で2人)
8〜15人：1:2:3 = 15:70:15 (70%で2人、15%で1人増減(1人か3人))
16〜20人：1:2:3:4:5 = 5:10:70:10:5 (70%で3人、10%で1人増減(2人か4人)、5%で2人増減(1人か5人))
21人〜：70%で基礎人数、10%で1人増減、5%で2人増減 (5人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([(人数 - 20) / 5]の切捨て) + 3
例)
24人：70%で3人、10%で1人増減(2人か4人)、5%で2人増減(1人か5人)
25人：70%で4人、10%で1人増減(3人か5人)、5%で2人増減(2人か6人)
30人：70%で5人、10%で1人増減(4人か6人)、5%で2人増減(3人か7人)
50人：70%で9人、10%で1人増減(8人か10人)、5%で2人増減(7人か11人)

・白狼の出現率
([参加人数 / 15] の切上げ)回数だけ判定を行います。
(15人なら1回、16人なら2回、50人なら3回)
判定を行うたびに、参加人数と同じ割合で1人、人狼と入れ替わります。
例)
15人：15%の判定を1回行う。
16人：16%の判定を2回行う。
30人：30%の判定を2回行う。
50人：50%の判定を3回行う。


・妖狐陣営
※15人未満はたまに出る程度、それ以降は出現確定
15人未満：0:1 = 90:10 (90%で0人、10%で1人)
16〜22人：1:2 = 90:10 (90%で1人、10%で2人)
23人〜：80%で基礎人数、10%で1人増減 (20人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 20]の切上げ)
例)
23人：80%で2人、10%で1人増減(1人か3人)
40人：80%で2人、10%で1人増減(1人か3人)
41人：80%で3人、10%で1人増減(2人か4人)
50人：80%で3人、10%で1人増減(2人か4人)

・子狐の出現率
20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら16%、50人なら50%)
最大出現人数は1人です。
子狐が出現した場合は出現人数と同じだけ妖狐が減ります。


・恋人陣営
※現在の仕様では実質キューピッドの人数です。
※増減の確率の関係で確実に出現するのは40人以上となります。
(キューピッドの出現自体をオプションで制御できるようにする予定)
10人未満：0:1 = 95:5 (95%で0人、5%で1人)
10〜16人：0:1 = 70:30 (70%で0人、30%で1人)
16〜22人：0:1:2 = 5:90:5 (90%で1人、5%で1人増減(0人か2人))
23人〜：90%で基礎人数、5%で1人増減 (20人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 20]の切捨て)
例)
23人：90%で1人、5%で1人増減(0人か2人)
40人：90%で2人、5%で1人増減(1人か3人)
50人：90%で3人、5%で1人増減(1人か3人)


・村人陣営
参加人数から人狼・妖狐・恋人陣営を差し引いた人数です。

・占い系
※占い師と魂の占い師がここに含まれます。
8人未満：0:1 = 10:90 (90%で1人、10%で0人)
8〜15人：1:2 = 95:5 (95%で1人、5%で2人)
16〜29人：1:2 = 90:10 (90%で1人、10%で2人)
30人〜：80%で基礎人数、10%で1人増減 (15人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 15]の切捨て)
例)
30人：80%で2人、10%で1人増減(1人か3人)
50人：80%で3人、10%で1人増減(2人か4人)

・魂の占い師の出現率
16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
魂の占い師が出現した場合は出現人数と同じだけ占い師が減ります。

・巫女
※キューピッドが出現している場合は確実に出現します。
　(ランダムで0人に当たっても強制的に1人に補正されます)
　(ただし、巫女が出現してもキューピッドが出現しているとは限りません)
9人未満：0:1 = 30:70 (70%で1人、30%で0人)
9〜15人：0:1:2 = 10:80:10 (80%で1人、10%で1人増減(0人か2人)
16人〜：80%で基礎人数、10%で1人増減 (15人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 15]の切捨て)
例)
29人：80%で1人、10%で1人増減(0人か2人)
30人：80%で2人、10%で1人増減(1人か3人)
50人：80%で3人、10%で1人増減(2人か4人)

・霊能系
・現在は霊能者のみがここに含まれます。
9人未満：0:1 = 10:90 (90%で1人、10%で0人)
9〜15人：1:2 = 95:5 (95%で1人、5%で2人)
16〜29人：1:2 = 90:10 (90%で1人、10%で2人)
30人〜：80%で基礎人数、10%で1人増減 (15人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 15]の切捨て)
例)
30人：80%で2人、10%で1人増減(1人か3人)
50人：80%で3人、10%で1人増減(2人か4人)

・狂人系
・狂人と狂信者がここに含まれます。
10人未満：0:1 = 70:30 (70%で0人、30%で1人)
10〜15人：0:1:2 = 10:80:10 (80%で1人、10%で1人増減(0人か2人)
16人〜：80%で基礎人数、10%で1人増減 (15人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 15]の切捨て)
例)
29人：80%で1人、10%で1人増減(0人か2人)
30人：80%で2人、10%で1人増減(1人か3人)
50人：80%で3人、10%で1人増減(2人か4人)

・狂信者の出現率
16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
狂信者が出現した場合は出現人数と同じだけ狂人が減ります。

・狩人系
・狩人と騎士がここに含まれます。
11人未満：0:1 = 90:10 (90%で0人、10%で1人)
11〜15人：0:1:2 = 10:80:10 (80%で1人、10%で1人増減(0人か2人)
16人〜：80%で基礎人数、10%で1人増減 (15人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 15]の切捨て)
例)
29人：80%で1人、10%で1人増減(0人か2人)
30人：80%で2人、10%で1人増減(1人か3人)
50人：80%で3人、10%で1人増減(2人か4人)

・騎士の出現率
20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
騎士が出現した場合は出現人数と同じだけ狩人と埋毒者が減ります。

・共有者
13人未満：0:1 = 90:10 (90%で0人、10%で1人)
13〜22人：1:2:3 = 10:80:10 (80%で2人、10%で1人増減(1人か3人)
23人〜：80%で基礎人数、10%で1人増減 (15人増えるごとに基礎人数が1人ずつ増加)
基礎人数 = ([人数 / 15]の切捨て) + 1
例)
29人：80%で2人、10%で1人増減(1人か3人)
30人：80%で3人、10%で1人増減(2人か4人)
50人：80%で4人、10%で1人増減(3人か5人)

・埋毒者
・騎士が出現していた場合はその人数分だけ埋毒者が減ります。
15人未満：0:1 = 95:5 (95%で0人、5%で1人)
16〜19人：0:1 = 85:15 (85%で0人、15%で1人
20人〜：20人増えるごとに基礎人数が1人ずつ増加
80%で基礎人数、10%で1人増減
基礎人数 = ([人数 / 20]の切捨て)
例)
39人：80%で1人、10%で1人増減(0人か2人)
40人：80%で2人、10%で1人増減(1人か3人)
50人：80%で2人、10%で1人増減(1人か3人)

・出題者
30人未満：0:1 = 99:1 (99%で0人、1%で1人)
30人〜：30人増えるごとに基礎人数が1人ずつ増加
基礎人数 = ([人数 / 30]の切捨て) - 1
例)
50人：99%で0人、1%で1人
59人：99%で0人、1%で1人
60人：99%で1人、1%で2人

○配役例 (確率の高いケースだけを寄せ集めたもの)
11人：村人4　人狼2　占い1　巫女1　霊能1　狂人1　狩人1
16人：村人4　人狼3　占い1　巫女1　霊能1　狂人1　狩人1　共有2　妖狐1　キューピッド1
30人：村人8　人狼4　白狼1　占い2　巫女2　霊能2　狂人2　狩人2　共有3　埋毒者1　妖狐2　キューピッド1
50人：村人15　人狼7　白狼2　占い2　魂の占い師1　巫女3　霊能3　狂人2　狂信者1　狩人2　騎士1　共有4　埋毒者1　妖狐2　子狐1　キューピッド3

</pre>
</body></html>
