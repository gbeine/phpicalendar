<?php

// English language include
// For version 0.5 PHP iCalendar
//
// Translation by Jared Wangen (jared@silter.org)
// With the help of Eri Hayashiguchi
//
// Submit new translations to chad@chadsdomain.com



$day_lang			= "日";
$week_lang			= "週";
$month_lang			= "月";
$year_lang			= "年";
$calendar_lang		= "カレンダー";
$next_day_lang		= "翌日";
$next_month_lang	= "来月";
$next_week_lang		= "来週";
$next_year_lang		= "明年";
$last_day_lang		= "前日";
$last_month_lang	= "先月";
$last_week_lang		= "先週";
$last_year_lang		= "去年";
$subscribe_lang		= "追加";
$download_lang		= "ダウンロード";
$published_lang		= "公開日：";
$powered_by_lang 	= "Powered by";

$version_lang		= "0.5";
$event_lang			= "件名";
$event_start_lang	= "開始日";
$event_end_lang		= "終了日";
$date_lang			= "日付";
$summary_lang		= "一覧";
$all_day_lang		= "終日イベント";
$notes_lang			= "注釈";

$todays_lang		= "今日今週の予定";
$this_weeks_lang		= "今週の予定";
$this_months_lang	= "今月の予定";
$this_years_lang	= "今年の予定";
$today_lang			= "今日";
$this_week_lang		= "今週";
$this_month_lang	= "今月";
$this_year_lang		= "今年";

// Set Location for date formatting, check out: http://www.php.net/manual/en/function.setlocale.php
setlocale (LC_TIME, 'ja_JP');

// For time formatting, check out: http://www.php.net/manual/en/function.date.php
$timeFormat = "g:iA";

// For date formatting, cehck out: http://www.php.net/manual/en/function.strftime.php
$dateFormat_day = "%B %e %A";
$dateFormat_week = "%B %e";
$dateFormat_week_list = "%b %e %a";
$dateFormat_month = "%Y %B";
$dateFormat_month_list = "%B %e %A";

?>